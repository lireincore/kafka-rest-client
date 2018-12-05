<?php

namespace LireinCore\KafkaRestClient\Request;

class ConsumerSubscribeRequest
{
    /**
     * @var string
     */
    private $topicPattern;

    /**
     * @var string[]
     */
    private $topics = [];

    /**
     * @param string|null $topicPattern
     */
    public function __construct(?string $topicPattern = null)
    {
        $this->topicPattern = $topicPattern;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function addTopic(string $name) : self
    {
        $this->topics[] = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function body() : array
    {
        if (is_null($this->topicPattern)) {
            return [
                'topics' => $this->topics
            ];
        } else {
            return [
                'topic_pattern' => $this->topicPattern
            ];
        }
    }
}