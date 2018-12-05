<?php

namespace LireinCore\KafkaRestClient;

class Message
{
    /**
     * @var string
     */
    private $topic;

    /**
     * @var mixed
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int
     */
    private $partition;

    /**
     * @var int
     */
    private $offset;

    /**
     * @param string $topic
     * @param mixed $key
     * @param mixed $value
     * @param int $partition
     * @param int $offset
     */
    public function __construct(string $topic, $key, $value, int $partition, int $offset)
    {
        $this->topic = $topic;
        $this->key = $key;
        $this->value = $value;
        $this->partition = $partition;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function topic() : string
    {
        return $this->topic;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function partition() : int
    {
        return $this->partition;
    }

    /**
     * @return int
     */
    public function offset() : int
    {
        return $this->offset;
    }
}