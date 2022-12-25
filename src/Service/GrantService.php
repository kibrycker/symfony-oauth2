<?php

namespace App\Service;

use League\Bundle\OAuth2ServerBundle\Controller\AuthorizationController;
use League\Bundle\OAuth2ServerBundle\Controller\TokenController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class GrantService
{
    /**
     * Определение зависимостей
     *
     * @param TokenController $tokenController Контроллер получения токена
     */
    public function __construct(
        private readonly TokenController $tokenController,
        private readonly AuthorizationController $authorizationController
    )
    {}

    /**
     * Получение токена
     *
     * @param Request $request Запрос
     *
     * @return array
     */
    public function getTokenClient(Request $request): array
    {
        $response = $this->tokenController->indexAction($request);
        $decode = new JsonDecode();
        $content = $response?->getContent();
        return $decode->decode($content, JsonEncoder::FORMAT, [
            'json_decode_associative' => true
        ]);
    }

    public function autorize(Request $request): array
    {
        $response = $this->authorizationController->indexAction($request);
        $decode = new JsonDecode();
        $content = $response?->getContent();
        return $decode->decode($content, JsonEncoder::FORMAT, [
            'json_decode_associative' => true
        ]);
    }
}