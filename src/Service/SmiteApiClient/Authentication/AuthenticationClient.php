<?php

namespace App\Service\SmiteApiClient\Authentication;

use App\Service\SmiteApiClient\AbstractHttpClient;
use App\Service\SmiteApiClient\Response;

class AuthenticationClient extends AbstractHttpClient
{
    /**
     * @param string $timestamp
     * @return Response
     */
    public function authenticate(string $timestamp): Response
    {
        $signature = $this->generateSignature('createsession', $timestamp);
        $uri = "/createsessionJson/{$this->client->getDevId()}/{$signature}/{$timestamp}";

        return $this->get($uri);
    }
}
