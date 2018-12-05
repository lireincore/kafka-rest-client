<?php

namespace LireinCore\KafkaRestClient\Request;

class ConsumerAssignmentRequest
{
    /**
     * @var \stdClass[]
     */
    private $partitions = [];

    /**
     * @param string $topic
     * @param int $id
     * @return ConsumerAssignmentRequest
     */
    public function addPartition(string $topic, int $id) : self
    {
        $partition = new \stdClass();
        $partition->topic = $topic;
        $partition->partition = $id;

        $this->partitions[] = $partition;

        return $this;
    }

    /**
     * @return array
     */
    public function body() : array
    {
        return [
            'partitions' => $this->partitions
        ];
    }
}