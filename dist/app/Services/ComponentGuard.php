<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 2016/7/26
 * Time: 15:31
 */

namespace Breeze\Wecom;

use EasyWeChat\Server\Guard;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\Log;

class ComponentGuard extends Guard
{
    const VERIFY_TICKET = 'component_verify_ticket';

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
            'to' => NULL,
            'from' => NULL,
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
        Log::debug('Message detail:', $message);
        $message = new Collection($message);

        // 保存票据 ComponentVerifyTicket
        // https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
        if ($message->get('InfoType') == self::VERIFY_TICKET) {
            ComponentVerifyTicket::setTicket($message['ComponentVerifyTicket']);
            return;
        }

        // 其他处理
        $handler = $this->messageHandler;
        if (!is_callable($handler)) {
            Log::debug('No handler enabled.');
            return;
        }
        $response = call_user_func_array($handler, [$message]);
        return $response;
    }
}