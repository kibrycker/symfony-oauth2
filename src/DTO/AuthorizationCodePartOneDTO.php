<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class AuthorizationCodePartOneDTO extends DataTransferObject
{
    /** @var string Тип гранта для предоставления токена учетных данных */
    public const GRANT_TYPE_RESPONSE_TYPE = 'code';

// with the value code

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string|null URI перенаправления клиента */
    public ?string $redirect_uri = null;

    /** @var string Разрешенные области */
    public string $scope;

    /** @var string|null Токен CSRF */
    public ?string $state = null;
}