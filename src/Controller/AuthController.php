<?php

namespace App\Controller;

use App\DTO\AuthorizationCodePartOneDTO;
use App\DTO\AuthorizationCodePartTwoDTO;
use App\DTO\ClientCredentialsDTO;
use App\DTO\PasswordCredentialsDTO;
use App\DTO\RefreshTokenDTO;
use App\Service\GrantService;
use Exception;
use JsonRpc\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Определение зависимостей
     *
     * @param GrantService $grantService Сервис получения токена
     */
    public function __construct(
        private readonly GrantService $grantService
    ) {}

    #[Route('/auth/token', name: 'api_auth')]
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
     * @throws Exception
     */
    public function client(ClientCredentialsDTO $params): array
    {
        $request = $this->getRequest($params, $params::GRANT_TYPE_CLIENT_CREDENTIALS);
        return $this->grantService->getToken($request);
    }

    /**
     * Получение токена для владельца ресурса
     *
     * @param PasswordCredentialsDTO $params Параметры предоставления владельца
     *
     * @return array
     * @throws Exception
     */
    public function password(PasswordCredentialsDTO $params): array
    {
        $request = $this->getRequest($params, $params::GRANT_TYPE_PASSWORD_CREDENTIALS);
        return $this->grantService->getToken($request);
    }

    /**
     * Предоставление кода авторизации
     *
     * @param AuthorizationCodePartTwoDTO $params Параметры кода авторизации
     *
     * @return array
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @throws Exception
     */
    public function authorize(AuthorizationCodePartTwoDTO $params): array
    {
        $request = $this->getRequest($params, $params::GRANT_TYPE_AUTHORIZATION_CODE);
        $queryParams = ['response_type' => AuthorizationCodePartOneDTO::GRANT_TYPE_RESPONSE_TYPE];
        $newDTO = new AuthorizationCodePartOneDTO($_GET);
        foreach ($newDTO as $key => $value) {
            $queryParams[$key] = $value;
        }
        $queryParams = array_merge($queryParams, [
            'code_challenge' => $newDTO->codeChallenge(),
        ]);
        $request->query->add($queryParams);
        return $this->grantService->authorize($request);
    }

    /**
     * Обновление токена
     *
     * @param RefreshTokenDTO $params Параметры для сброса токена
     *
     * @return array
     * @throws Exception
     */
    public function refresh(RefreshTokenDTO $params): array
    {
        $request = $this->getRequest($params, $params::GRANT_TYPE_REFRESH_TOKEN);
        return $this->grantService->getToken($request);
    }

    /**
     * Создание запроса для получения токена доступа
     *
     * @param array $params Входящие параметры
     * @param string $grantType Тип гранта доступа
     *
     * @return Request
     * @throws Exception
     */
    private function getRequest(mixed $params, string $grantType): Request
    {
        if (empty($params)) {
            throw new Exception('Parameters not passed', 500);
        }
        if (empty($grantType)) {
            throw new Exception('Access grant type not passed', 500);
        }

        $parameters = ['grant_type' => $grantType];
        foreach ($params as $prop => $value) {
            $parameters[$prop] = $value;
        }
        return Request::create($_SERVER['HTTP_HOST'] ?? '', 'POST', $parameters);
    }
}