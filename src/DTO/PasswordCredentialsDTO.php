<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * DTO параметров предоставления учетных данных клиента по паролю
 */
class PasswordCredentialsDTO extends DataTransferObject
{
    /** @var string Тип гранта для предоставления токена учетных данных */
    public const GRANT_TYPE_PASSWORD_CREDENTIALS = 'password';

    /** @var string Тип гранта для токена */
    public string $grant_type = self::GRANT_TYPE_PASSWORD_CREDENTIALS;

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string Ключ секрета клиента */
    public string $client_secret;

    /** @var string Разрешенные области */
    public string $scope;

    /** @var string Имя пользователя */
    public string $username;

    /** @var string Пароль */
    public string $password;
}