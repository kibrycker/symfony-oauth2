<?php

namespace App\Controller;

use App\DTO\ClientCredentialsDTO;
use App\Service\TokenService;
use JsonRpc\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Определение зависимостей
     *
     * @param TokenService $tokenService Сервис получения токена
     */
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    #[Route('/api/auth', name: 'api_auth')]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    /**
     * Получение учетных данных клиента путем межмашинной аутентификации
     *
     * @param ClientCredentialsDTO $params
     *
     * @return array
     */
    public function clientCredentialsGrant(ClientCredentialsDTO $params): array
    {
        $request = Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', [
            'grant_type' => $params->grant_type,
            'client_id' => $params->client_id,
            'client_secret' => $params->client_secret,
            'scope' => $params->scope
        ]);
        return $this->tokenService->getTokenClient($request);
    }
}