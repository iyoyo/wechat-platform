<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\Component;

use EasyWeChat\Support\Log;
use EasyWeChat\Support\XML;
use EasyWeChat\Message\Text;
use EasyWeChat\Support\Collection;
use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\Raw as RawMessage;
use Symfony\Component\HttpFoundation\Request;
use EasyWeChat\Core\Exceptions\FaultException;
use Symfony\Component\HttpFoundation\Response;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * 第三方平台消息事件处理.
 */
class Guard
{
    /**
     * Empty string.
     */
    const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * InfoType.
     */
    const MSG_VERIFY_TICKET = 'component_verify_ticket';
    const MSG_UNAUTHORIZED = 'unauthorized';
    const MSG_AUTHORIZED = 'authorized';
    const MSG_UPDATEAUTHORIZED = 'updateauthorized';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var string|callable
     */
    protected $messageHandler;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param string  $token
     * @param Request $request
     */
    public function __construct($token, Request $request = null)
    {
        $this->token = $token;
        $this->request = $request ?: Request::createFromGlobals();
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function debug($debug = true)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Handle and return response.
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function serve()
    {
        Log::debug('Request received:', [
            'Method' => $this->request->getMethod(),
            'URI' => $this->request->getRequestUri(),
            'Query' => $this->request->getQueryString(),
            'Protocal' => $this->request->server->get('SERVER_PROTOCOL'),
            'Content' => $this->request->getContent(),
        ]);

        $this->validate($this->token);

        if ($str = $this->request->get('echostr')) {
            Log::debug("Output 'echostr' is '$str'.");

            return new Response($str);
        }

        $result = $this->handleRequest();
        $response = $this->buildResponse($result['response']);

        Log::debug('Server response created:', compact('response'));

        return new Response($response);
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @throws FaultException
     */
    public function validate($token)
    {
        $params = [
            $token,
            $this->request->get('timestamp'),
            $this->request->get('nonce'),
        ];

        if (! $this->debug && $this->request->get('signature') !== $this->signature($params)) {
            throw new FaultException('Invalid request signature.', 400);
        }
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     * @param int      $option
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function setMessageHandler($callback = null)
    {
        if (! is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->messageHandler = $callback;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageHandler()
    {
        return $this->messageHandler;
    }

    /**
     * Request getter.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Request setter.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Encryptor.
     *
     * @param Encryptor $encryptor
     *
     * @return Guard
     */
    public function setEncryptor(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    /**
     * Return the encryptor instance.
     *
     * @return Encryptor
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }

    /**
     * Build response.
     *
     * @param $to
     * @param $from
     * @param mixed $message
     *
     * @return string
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    protected function buildResponse($message)
    {
        if (empty($message) || $message === self::SUCCESS_EMPTY_RESPONSE) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text(['content' => $message]);
        }

        if (! $this->isMessage($message)) {
            $messageType = gettype($message);
            throw new InvalidArgumentException("Invalid Message type .'{$messageType}'");
        }

        $response = $this->buildReply($message);

        if ($this->isSafeMode()) {
            Log::debug('Message safe mode is enable.');
            $response = $this->encryptor->encryptMsg(
                $response,
                $this->request->get('nonce'),
                $this->request->get('timestamp')
            );
        }

        return $response;
    }

    /**
     * Whether response is message.
     *
     * @param mixed $message
     *
     * @return bool
     */
    protected function isMessage($message)
    {
        if (is_array($message)) {
            foreach ($message as $element) {
                if (! is_subclass_of($element, AbstractMessage::class)) {
                    return false;
                }
            }

            return true;
        }

        return is_subclass_of($message, AbstractMessage::class);
    }

    /**
     * Get request message.
     *
     * @return array
     *
     * @throws BadRequestException
     */
    public function getMessage()
    {
        $message = $this->parseMessageFromRequest($this->request->getContent(false));

        if (! is_array($message) || empty($message)) {
            throw new BadRequestException('Invalid request.');
        }

        return $message;
    }

    /**
     * Handle request.
     *
     * @return array
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     * @throws \EasyWeChat\Server\BadRequestException
     */
    protected function handleRequest()
    {
        $message = $this->getMessage();
        $response = $this->handleMessage($message);

        return [
            'response' => $response,
        ];
    }

    /**
     * Handle message.
     *
     * @param array $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        $handler = $this->messageHandler;

        if (! is_callable($handler)) {
            Log::debug('No handler enabled.');

            return;
        }

        Log::debug('Message detail:', $message);

        $message = new Collection($message);
        $response = call_user_func_array($handler, [$message]);

        return $response;
    }

    /**
     * Build reply XML.
     *
     * @param AbstractMessage $message
     *
     * @return string
     */
    protected function buildReply($message)
    {
        $base = [
            'CreateTime' => time(),
        ];

        $transformer = new Transformer();

        return XML::build(array_merge($base, $transformer->transform($message)));
    }

    /**
     * Get signature.
     *
     * @param array $request
     *
     * @return string
     */
    protected function signature($request)
    {
        sort($request, SORT_STRING);

        return sha1(implode($request));
    }

    /**
     * Parse message array from raw php input.
     *
     * @param string|resource $content
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     * @throws \EasyWeChat\Encryption\EncryptionException
     *
     * @return array
     */
    protected function parseMessageFromRequest($content)
    {
        $content = strval($content);

        if ($this->isSafeMode()) {
            if (! $this->encryptor) {
                throw new RuntimeException('Safe mode Encryptor is necessary, please use Guard::setEncryptor(Encryptor $encryptor) set the encryptor instance.');
            }

            $message = $this->encryptor->decryptMsg(
                $this->request->get('msg_signature'),
                $this->request->get('nonce'),
                $this->request->get('timestamp'),
                $content
            );
        } else {
            $message = XML::parse($content);
        }

        return $message;
    }

    /**
     * Check the request message safe mode.
     *
     * @return bool
     */
    private function isSafeMode()
    {
        return $this->request->get('encrypt_type') && $this->request->get('encrypt_type') === 'aes';
    }
}
