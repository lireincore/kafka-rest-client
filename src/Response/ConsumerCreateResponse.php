<?php

namespace LireinCore\KafkaRestClient\Response;

class ConsumerCreateResponse
{
    /**
     * @var string
     */
    private $instanceId;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @param string $instanceId
     * @param string $baseUri
     */
    public function __construct(string $instanceId, string $baseUri)
    {
        $this->instanceId = $instanceId;
        $this->baseUri = $baseUri;
    }

    /**
     * @return string
     */
    public function instanceId() : string
    {
        return $this->instanceId;
    }

    /**
     * @return string
     */
    public function baseUri() : string
    {
        return $this->baseUri;
    }
}