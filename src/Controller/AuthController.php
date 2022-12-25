<?php

namespace App\Controller;

use App\DTO\AuthorizationCodePartOneDTO;
use App\DTO\AuthorizationCodePartTwoDTO;
use App\DTO\ClientCredentialsDTO;
use App\DTO\PasswordCredentialsDTO;
use App\DTO\RefreshTokenDTO;
use App\Service\GrantService;
use JsonRpc\Controller;
use League\Bundle\OAuth2ServerBundle\Controller\AuthorizationController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class AuthController extends Controller
{
    /**
     * Определение зависимостей
     *
     * @param GrantService $grantService Сервис получения токена
     */
    public function __construct(
        private readonly GrantService            $grantService
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
        $parameters = ['grant_type' => $params::GRANT_TYPE_CLIENT_CREDENTIALS];
        foreach ($params as $prop => $value) {
            $parameters[$prop] = $value;
        }
        $request = Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', $parameters);
        return $this->grantService->getTokenClient($request);
    }

    /**
     * Получение токена для владельца ресурса
     *
     * @param PasswordCredentialsDTO $params Параметры предоставления владельца
     *
     * @return array
     */
    public function passwordCredentials(PasswordCredentialsDTO $params): array
    {
        $parameters = ['grant_type' => $params::GRANT_TYPE_PASSWORD_CREDENTIALS];
        foreach ($params as $prop => $value) {
            $parameters[$prop] = $value;
        }
        $request = Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', $parameters);
        return $this->grantService->getTokenClient($request);
    }

    /**
     * Предоставление кода авторизации
     *
     * @param AuthorizationCodePartTwoDTO $params Параметры кода авторизации
     *
     * @return array
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function authorize(AuthorizationCodePartTwoDTO $params): array
    {
        $parameters = ['grant_type' => $params::GRANT_TYPE_AUTHORIZATION_CODE];
        foreach ($params as $prop => $value) {
            $parameters[$prop] = $value;
        }
        $request = Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', $parameters);
        $queryParams = ['response_type' => AuthorizationCodePartOneDTO::GRANT_TYPE_RESPONSE_TYPE];
        $newDTO = new AuthorizationCodePartOneDTO($_GET);
        foreach ($newDTO as $key => $value) {
            $queryParams[$key] = $value;
        }
        $request->query->add($queryParams);
        return $this->grantService->authorize($request);
    }

    /**
     * Обновление токена
     *
     * @param RefreshTokenDTO $params Параметры для сброса токена
     *
     * @return array
     */
    public function refreshToken(RefreshTokenDTO $params): array
    {
        $parameters = ['grant_type' => $params::GRANT_TYPE_REFRESH_TOKEN];
        foreach ($params as $prop => $value) {
            $parameters[$prop] = $value;
        }
        $request = Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', $parameters);
        return $this->grantService->getTokenClient($request);
    }

}