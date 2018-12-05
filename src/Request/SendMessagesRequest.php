<?php

namespace LireinCore\KafkaRestClient\Request;

use LireinCore\KafkaRestClient\Client;
use LireinCore\KafkaRestClient\Schema;

class SendMessagesRequest
{
    /**
     * @var string
     */
    private $topic;

    /**
     * @var Schema|null
     */
    private $keySchema;

    /**
     * @var int|null
     */
    private $keySchemaId;

    /**
     * @var Schema|null
     */
    private $valueSchema;

    /**
     * @var int|null
     */
    private $valueSchemaId;

    /**
     * @var \stdClass[]
     */
    private $records = [];

    /**
     * @var string
     */
    private $contentTypeHeader;

    /**
     * @param string $topic
     * @param Schema|null $keySchema
     * @param int|null $keySchemaId
     * @param Schema|null $valueSchema
     * @param int|null $valueSchemaId
     * @param string $contentTypeHeader
     */
    public function __construct(
        string $topic,
        ?Schema $keySchema = null,
        ?int $keySchemaId = null,
        ?Schema $valueSchema = null,
        ?int $valueSchemaId = null,
        string $contentTypeHeader = Client::TYPE_KAFKA_JSON
    )
    {
        if (!in_array($contentTypeHeader, [Client::TYPE_KAFKA_JSON, Client::TYPE_KAFKA_AVRO, Client::TYPE_KAFKA_BINARY], true)) {
            throw new \InvalidArgumentException('Invalid contentTypeHeader');
        }

        $this->topic = $topic;
        $this->keySchema = $keySchema;
        $this->keySchemaId = $keySchemaId;
        $this->valueSchema = $valueSchema;
        $this->valueSchemaId = $valueSchemaId;
        $this->contentTypeHeader = $contentTypeHeader;
    }

    /**
     * @param mixed $value
     * @param mixed|null $key
     * @param int|null $partition
     * @return $this
     */
    public function addRecord($value, $key = null, ?int $partition = null) : self
    {
        if ($this->contentTypeHeader === Client::TYPE_KAFKA_BINARY) {
            $encodeFn = function ($str) {
                return base64_encode($str);
            };
        } elseif ($this->contentTypeHeader === Client::TYPE_KAFKA_JSON) {
            $encodeFn = function ($str) {
                return json_encode($str);
            };
        } else {
            $encodeFn = function ($str) {
                return $str;
            };
        }

        $record = new \stdClass();
        if ($key !== null) {
            $record->key = $encodeFn($key);
        }
        $record->value = $encodeFn($value);
        if ($partition !== null) {
            $record->partition = $partition;
        }

        $this->records[] = $record;

        return $this;
    }

    /**
     * @return string
     */
    public function topic() : string
    {
        return $this->topic;
    }

    /**
     * @return array
     */
    public function body() : array
    {
        $data = [
            'records' => $this->records
        ];

        if (!is_null($this->keySchema)) {
            $data['key_schema'] = json_encode($this->keySchema);
        }

        if (!is_null($this->keySchemaId)) {
            $data['key_schema_id'] = $this->keySchemaId;
        }

        if (!is_null($this->valueSchema)) {
            $data['value_schema'] = json_encode($this->valueSchema);
        }

        if (!is_null($this->valueSchemaId)) {
            $data['value_schema_id'] = $this->valueSchemaId;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function contentTypeHeader() : string
    {
        return $this->contentTypeHeader;
    }
}