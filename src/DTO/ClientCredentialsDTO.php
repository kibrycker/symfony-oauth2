<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * DTO параметров предоставления учетных данных клиента
 */
class ClientCredentialsDTO extends DataTransferObject
{
    /** @var string Тип предоставления */
    public string $grant_type = 'client_credentials';

    /** @var string Идентификатор клиента */
    public string $client_id;

    /** @var string Ключ секрета клиента */
    public string $client_secret;

    /** @var string Разрешенные области */
    public string $scope;

}