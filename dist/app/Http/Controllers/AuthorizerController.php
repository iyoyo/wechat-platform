<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-12-12
 * Time: 18:53
 */

namespace Wechat\Http\Controllers;


use Wechat\Repositories\AuthorizerRepository;

class AuthorizerController extends Controller
{
    protected $repository;

    public function __construct(
        AuthorizerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        if ($clientId = request('client_id')) {
            return $this->repository->getAuthorizersByClient($clientId);
        }
        return '';
    }
}