<?php

    namespace CommissionCalc\Models;

class Client
{
    const CLIENT_TYPE_NATURAL = 'natural';
    const CLIENT_TYPE_LEGAL = 'legal';

    private $clientId;
    private $clientType;

    public function __construct(int $clientId, string $clientType)
    {
        if (!in_array($clientType, [self::CLIENT_TYPE_NATURAL, self::CLIENT_TYPE_LEGAL])) {
            throw new Exception("Unknown client type $clientType");
        }
        $this->clientType = $clientType;
        $this->clientId = $clientId;
    }

    public function getClientType()
    {
        return $this->clientType;
    }

    public function getClientId()
    {
        return $this->clientId;
    }
}
