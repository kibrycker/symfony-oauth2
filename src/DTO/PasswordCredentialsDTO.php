<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * DTO параметров предоставления учетных данных клиента по паролю
 */
class PasswordCredentialsDTO extends DataTransferObject
{
    /** @var string Тип гранта для предоставления токена учетных данных */
    public const GRANT_TYPE_PASSWORD_CREDENTIALS = 'client_credentials';

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string Ключ секрета клиента */
    public string $client_secret;

    /** @var string Разрешенные области */
    public string $scope;
}