<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * DTO параметров для авторизации
 */
class AuthorizationCodeDTO extends DataTransferObject
{
    /** @var string Тип гранта для предоставления токена учетных данных */
    public const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string Ключ секрета клиента */
    public string $client_secret;

    /** @var string|null URI перенаправления клиента */
    public ?string $redirect_uri = null;

    /** @var string Код авторизации из строки запроса */
    public string $code;
}