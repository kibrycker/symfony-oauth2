<?php

namespace App\Service;

use League\Bundle\OAuth2ServerBundle\Event\TokenRequestResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TokenService
{
    /**
     * Определение зависимостей
     *
     * @param AuthorizationServer $server
     * @param ServerRequestFactoryInterface $serverRequestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UploadedFileFactoryInterface $uploadedFileFactory
     * @param ResponseFactoryInterface $responseFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private readonly AuthorizationServer $server,
        private readonly ServerRequestFactoryInterface $serverRequestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UploadedFileFactoryInterface $uploadedFileFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EventDispatcherInterface $eventDispatcher
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
        $factory = new PsrHttpFactory(
            $this->serverRequestFactory,
            $this->streamFactory,
            $this->uploadedFileFactory,
            $this->responseFactory
        );
        $serverRequest = $factory->createRequest($request);
        $serverResponse = $this->responseFactory->createResponse();

        try {
            $response = $this->server->respondToAccessTokenRequest($serverRequest, $serverResponse);
        } catch (OAuthServerException $e) {
            $response = $e->generateHttpResponse($serverResponse);
        }

        $foundationFactory = new HttpFoundationFactory();
        $renderedResponse = $foundationFactory->createResponse($response);

        $event = $this->eventDispatcher->dispatch(
            new TokenRequestResolveEvent($renderedResponse),
            OAuth2Events::TOKEN_REQUEST_RESOLVE
        );

        $decode = new JsonDecode();
        $content = $event->getResponse()?->getContent();
        return $decode->decode($content, JsonEncoder::FORMAT, ['json_decode_associative' => true]);
    }
}