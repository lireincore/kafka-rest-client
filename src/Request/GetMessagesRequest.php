<?php

namespace LireinCore\KafkaRestClient\Request;

use LireinCore\KafkaRestClient\Client;

class GetMessagesRequest
{
    /**
     * @var int|null
     */
    private $timeout;

    /**
     * @var int|null
     */
    private $maxBytes;

    /**
     * @var string
     */
    private $acceptHeader;

    /**
     * @param int|null $timeout
     * @param int|null $maxBytes
     * @param string $acceptHeader
     */
    public function __construct(?int $timeout = null, ?int $maxBytes = null, string $acceptHeader = Client::TYPE_KAFKA_JSON)
    {
        if (!in_array($acceptHeader, [Client::TYPE_KAFKA_JSON, Client::TYPE_KAFKA_AVRO, Client::TYPE_KAFKA_BINARY], true)) {
            throw new \InvalidArgumentException('Invalid acceptHeader');
        }

        $this->timeout = $timeout;
        $this->maxBytes = $maxBytes;
        $this->acceptHeader = $acceptHeader;
    }

    /**
     * @return int|null
     */
    public function timeout() : ?int
    {
        return $this->timeout;
    }

    /**
     * @return int|null
     */
    public function maxBytes() : ?int
    {
        return $this->maxBytes;
    }

    /**
     * @return string
     */
    public function acceptHeader() : string
    {
        return $this->acceptHeader;
    }
}