<?php

namespace LireinCore\KafkaRestClient\Response;

use LireinCore\KafkaRestClient\Offset;

class SendMessagesResponse
{
    /**
     * @var int|null
     */
    private $keySchemaId;

    /**
     * @var int|null
     */
    private $valueSchemaId;

    /**
     * @var Offset[]
     */
    private $offsets;

    /**
     * @param int|null $keySchemaId
     * @param int|null $valueSchemaId
     * @param Offset[] $offsets
     */
    public function __construct(?int $keySchemaId = null, ?int $valueSchemaId = null, array $offsets = [])
    {
        $this->keySchemaId = $keySchemaId;
        $this->valueSchemaId = $valueSchemaId;
        $this->offsets = $offsets;
    }

    /**
     * @return int|null
     */
    public function keySchemaId() : ?int
    {
        return $this->keySchemaId;
    }

    /**
     * @return int|null
     */
    public function valueSchemaId() : ?int
    {
        return $this->valueSchemaId;
    }

    /**
     * @return Offset[]
     */
    public function offsets() : array
    {
        return $this->offsets;
    }
}