<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * DTO параметров для сброса токена
 */
class RefreshTokenDTO extends DataTransferObject
{
    /** @var string Тип гранта для обновления токена */
    public const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string Секрет клиента */
    public string $client_secret;

    /** @var string Разрешенные области */
    public string $scope;
}