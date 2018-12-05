<?php

namespace LireinCore\KafkaRestClient\Request;

class ConsumerCommitOffsetsRequest
{
    /**
     * @var \stdClass[]
     */
    private $offsets = [];

    /**
     * @param string $topic
     * @param int $partition
     * @param int $offsetValue
     * @return ConsumerCommitOffsetsRequest
     */
    public function addOffset(string $topic, int $partition, int $offsetValue) : self
    {
        $offset = new \stdClass();
        $offset->topic = $topic;
        $offset->partition = $partition;
        $offset->offset = $offsetValue;
        $this->offsets[] = $offset;

        return $this;
    }

    /**
     * @return array
     */
    public function body() : array
    {
        return [
            'offsets' => $this->offsets
        ];
    }
}