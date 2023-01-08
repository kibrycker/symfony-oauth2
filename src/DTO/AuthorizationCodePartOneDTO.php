<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class AuthorizationCodePartOneDTO extends DataTransferObject
{
    /** @var string Тип ответа для передачи в query_string */
    public const RESPONSE_TYPE = 'code';

    /** @var string Тип ответа */
    public string $response_type = self::RESPONSE_TYPE;

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string|null URI перенаправления клиента */
    public ?string $redirect_uri = null;

    /** @var string Разрешенные области */
    public string $scope;

    /** @var string|null Токен CSRF */
    public ?string $state = null;

    /** @var string|null Код вызова */
    public ?string $code_challenge = null;

    /**
     * Получение кода вызова
     * @return string
     */
    public function codeChallenge(): string
    {
        return rtrim(strtr(base64_encode($this->client_id), '+/', '-_'), '=');
    }
}