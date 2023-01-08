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
        $request = $this->getRequest($params);
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
        $request = $this->getRequest($params);
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
        $request = $this->getRequest($params);
        $paramsOne = new AuthorizationCodePartOneDTO($_GET);
        $paramsOne->code_challenge = $paramsOne->codeChallenge();
        $request->query->add($paramsOne->toArray());
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
        $request = $this->getRequest($params);
        return $this->grantService->getToken($request);
    }

    /**
     * Создание запроса для получения токена доступа
     *
     * @param array $params Входящие параметры
     *
     * @return Request
     * @throws Exception
     */
    private function getRequest(mixed $params): Request
    {
        if (empty($params)) {
            throw new Exception('Parameters not passed', 500);
        }

        return Request::create(
            $_SERVER['HTTP_HOST'] ?? '',
            'POST',
            $params->toArray()
        );
    }
}