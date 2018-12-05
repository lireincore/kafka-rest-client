<?php

namespace LireinCore\KafkaRestClient;

class Offset
{
    /**
     * @var int|null
     */
    private $partition;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var int|null
     */
    private $errorCode;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @param int|null $partition
     * @param int|null $offset
     * @param int|null $errorCode
     * @param string|null $error
     */
    public function __construct(?int $partition, ?int $offset, ?int $errorCode, ?string $error)
    {
        $this->partition = $partition;
        $this->offset = $offset;
        $this->errorCode = $errorCode;
        $this->error = $error;
    }

    /**
     * @return int|null
     */
    public function partition() : ?int
    {
        return $this->partition;
    }

    /**
     * @return int|null
     */
    public function offset() : ?int
    {
        return $this->offset;
    }

    /**
     * @return int|null
     */
    public function errorCode() : ?int
    {
        return $this->errorCode;
    }

    /**
     * @return string|null
     */
    public function error() : ?string
    {
        return $this->error;
    }
}