<?php

namespace LireinCore\KafkaRestClient;

use Psr\Log\LoggerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Client
{
    public const TYPE_KAFKA = 'application/vnd.kafka.v2+json';
    public const TYPE_KAFKA_JSON = 'application/vnd.kafka.json.v2+json';
    public const TYPE_KAFKA_AVRO = 'application/vnd.kafka.avro.v2+json';
    public const TYPE_KAFKA_BINARY = 'application/vnd.kafka.binary.v2+json';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var string
     */
    private $restHost;

    /**
     * @var string|null
     */
    private $schemaHost;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param string $restHost
     * @param string|null $schemaHost
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $restHost,
        ?string $schemaHost = null,
        ?LoggerInterface $logger = null
    )
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->restHost = $restHost;
        $this->schemaHost = $schemaHost;
        $this->logger = $logger;
    }

    /**
     * @param string $restHost
     */
    public function setRestHost(string $restHost) : void
    {
        $this->restHost = $restHost;
    }

    /**
     * @return string
     */
    public function restHost() : string
    {
        return $this->restHost;
    }

    /**
     * @param string $schemaHost
     */
    public function setSchemaHost(string $schemaHost) : void
    {
        $this->schemaHost = $schemaHost;
    }

    /**
     * @param string $method
     * @param string $url
     * @return RequestInterface
     */
    public function createRequest(string $method, string $url) : RequestInterface
    {
        return $this->requestFactory->createRequest($method, $url);
    }

    /**
     * @param string $content
     * @return StreamInterface
     */
    public function createStream(string $content) : StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }

    /**
     * @param RequestInterface $request
     * @return mixed|null
     * @throws KafkaRestException
     */
    public function sendRequest(RequestInterface $request)
    {
        try {
            $response = $this->client->sendRequest($request);
            if (204 !== $response->getStatusCode()) {
                $content = $response->getBody()->getContents();
                $content = json_decode($content, true);
                if (200 !== $response->getStatusCode()) {
                    $message = $content['message'] ?? 'Error';
                    $code = $content['error_code'] ?? 0;
                    if ($this->logger) {
                        $this->logger->error('Kafka rest error: ' . $message . ' (code: ' . $code . ')');
                    }
                    throw new KafkaRestException($message, $code);
                }
                return $content;
            }
            return null;
        } catch (ClientExceptionInterface $ex) {
            if ($this->logger) {
                $this->logger->error('Client error: ' . $ex->getMessage());
            }
            throw new KafkaRestException('Client error', 0, $ex);
        }
    }
}