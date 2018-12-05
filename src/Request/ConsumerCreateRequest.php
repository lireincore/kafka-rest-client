<?php

namespace LireinCore\KafkaRestClient\Request;

class ConsumerCreateRequest
{
    public const FORMAT_JSON = 'json';
    public const FORMAT_BINARY = 'binary';
    public const FORMAT_AVRO = 'avro';

    public const AUTO_OFFSET_RESET_LATEST = 'latest';
    public const AUTO_OFFSET_RESET_EARLIEST = 'earliest';
    public const AUTO_OFFSET_RESET_NONE = 'none';

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string|null
     */
    private $consumerName;

    /**
     * @var string|null
     */
    private $format;

    /**
     * @var string
     */
    private $autoOffsetReset;

    /**
     * @var bool
     */
    private $autoCommitEnable;

    /**
     * @param string $groupName
     * @param string|null $consumerName
     * @param string|null $format
     * @param string $autoOffsetReset
     * @param bool $autoCommitEnable
     */
    public function __construct(
        string $groupName,
        ?string $consumerName = null,
        ?string $format = self::FORMAT_JSON,
        string $autoOffsetReset = self::AUTO_OFFSET_RESET_EARLIEST,
        bool $autoCommitEnable = false
    )
    {
        if (!in_array($autoOffsetReset, [self::AUTO_OFFSET_RESET_LATEST, self::AUTO_OFFSET_RESET_EARLIEST, self::AUTO_OFFSET_RESET_NONE], true)) {
            throw new \InvalidArgumentException('Invalid autoOffsetReset');
        }

        if (!is_null($format) && !in_array($format, [self::FORMAT_JSON, self::FORMAT_BINARY, self::FORMAT_AVRO], true)) {
            throw new \InvalidArgumentException('Invalid format');
        }

        $this->groupName = $groupName;
        $this->consumerName = $consumerName;
        $this->format = $format;
        $this->autoOffsetReset = $autoOffsetReset;
        $this->autoCommitEnable = $autoCommitEnable;
    }

    /**
     * @return string
     */
    public function groupName() : string
    {
        return $this->groupName;
    }

    /**
     * @return array
     */
    public function body() : array
    {
        $data = [
            'auto.offset.reset' => $this->autoOffsetReset,
            'auto.commit.enable' => $this->autoCommitEnable ? 'true' : 'false',
        ];
        if (!is_null($this->consumerName)) {
            $data['name'] = $this->consumerName;
        }
        if (!is_null($this->format)) {
            $data['format'] = $this->format;
        }

        return $data;
    }
}