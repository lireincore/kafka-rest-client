<?php

namespace LireinCore\KafkaRestClient;

use LireinCore\KafkaRestClient\Request\ConsumerAssignmentRequest;
use LireinCore\KafkaRestClient\Request\ConsumerCommitOffsetsRequest;
use LireinCore\KafkaRestClient\Request\ConsumerSubscribeRequest;
use LireinCore\KafkaRestClient\Request\GetMessagesRequest;
use LireinCore\KafkaRestClient\Request\ConsumerCreateRequest;
use LireinCore\KafkaRestClient\Response\ConsumerCreateResponse;

class Consumer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ConsumerCreateRequest $request
     * @return ConsumerCreateResponse
     * @throws KafkaRestException
     */
    public function create(ConsumerCreateRequest $request) : ConsumerCreateResponse
    {
        $groupName = $request->groupName();
        $url = $this->client->restHost() . "/consumers/{$groupName}";
        $clientRequest = $this->client->createRequest('POST', $url);
        $clientRequest = $clientRequest->withHeader('Content-Type', Client::TYPE_KAFKA);
        $content = json_encode($request->body());
        $stream = $this->client->createStream($content);
        $clientRequest = $clientRequest->withBody($stream);
        $result = $this->client->sendRequest($clientRequest);

        $response = new ConsumerCreateResponse($result['instance_id'], $result['base_uri']);

        return $response;
    }

    /**
     * @param ConsumerSubscribeRequest $request
     * @param ConsumerCreateResponse $response
     * @throws KafkaRestException
     */
    public function subscribe(ConsumerSubscribeRequest $request, ConsumerCreateResponse $response) : void
    {
        $url = $response->baseUri() . '/subscription';
        $clientRequest = $this->client->createRequest('POST', $url);
        $clientRequest = $clientRequest->withHeader('Content-Type', Client::TYPE_KAFKA);
        $content = json_encode($request->body());
        $stream = $this->client->createStream($content);
        $clientRequest = $clientRequest->withBody($stream);
        $this->client->sendRequest($clientRequest);
    }

    /**
     * @param ConsumerAssignmentRequest $request
     * @param ConsumerCreateResponse $response
     * @throws KafkaRestException
     */
    public function assign(ConsumerAssignmentRequest $request, ConsumerCreateResponse $response) : void
    {
        $url = $response->baseUri() . '/assignments';
        $clientRequest = $this->client->createRequest('POST', $url);
        $clientRequest = $clientRequest->withHeader('Content-Type', Client::TYPE_KAFKA);
        $content = json_encode($request->body());
        $stream = $this->client->createStream($content);
        $clientRequest = $clientRequest->withBody($stream);
        $this->client->sendRequest($clientRequest);
    }

    /**
     * @param ConsumerCreateResponse $response
     * @return string[]
     * @throws KafkaRestException
     */
    public function getSubscribe(ConsumerCreateResponse $response) : array
    {
        $url = $response->baseUri() . '/subscription';
        $clientRequest = $this->client->createRequest('GET', $url);
        $clientRequest = $clientRequest->withHeader('Accept', Client::TYPE_KAFKA);
        $result = $this->client->sendRequest($clientRequest);
        $response = $result['topics'];

        return $response;
    }


    /**
     * @param GetMessagesRequest $request
     * @param ConsumerCreateResponse $response
     * @return Message[]
     * @throws KafkaRestException
     */
    public function pool(GetMessagesRequest $request, ConsumerCreateResponse $response) : array
    {
        $params = [];
        if (null !== $request->timeout()) {
            $params[] = "timeout={$request->timeout()}";
        }
        if (null !== $request->maxBytes()) {
            $params[] = "max_bytes={$request->timeout()}";
        }
        if ($params) {
            $query = '?' . implode('&', $params);
        } else {
            $query = '';
        }
        $url = $response->baseUri() . '/records' . $query;
        $clientRequest = $this->client->createRequest('GET', $url);

        $clientRequest = $clientRequest->withHeader('Accept', $request->acceptHeader());
        $result = $this->client->sendRequest($clientRequest);
        if ($request->acceptHeader() === Client::TYPE_KAFKA_BINARY) {
            $decodeFn = function ($str) {
                return base64_decode($str);
            };
        } elseif ($request->acceptHeader() === Client::TYPE_KAFKA_JSON) {
            $decodeFn = function ($str) {
                return json_decode($str, true);
            };
        } else {
            $decodeFn = function ($str) {
                return $str;
            };
        }

        $records = [];
        foreach ($result as $record) {
            $records[] = new Message(
                $record['topic'],
                $decodeFn($record['key']),
                $decodeFn($record['value']),
                $record['partition'],
                $record['offset']
            );
        }

        return $records;
    }

    /**
     * @param ConsumerCommitOffsetsRequest $request
     * @param ConsumerCreateResponse $response
     * @throws KafkaRestException
     */
    public function commit(ConsumerCommitOffsetsRequest $request, ConsumerCreateResponse $response) : void
    {
        $url = $response->baseUri() . '/offsets';
        $clientRequest = $this->client->createRequest('POST', $url);
        $clientRequest = $clientRequest->withHeader('Content-Type', Client::TYPE_KAFKA);
        $content = json_encode($request->body());
        $stream = $this->client->createStream($content);
        $clientRequest = $clientRequest->withBody($stream);
        $this->client->sendRequest($clientRequest);
    }

    /**
     * @param ConsumerCreateResponse $response
     * @throws KafkaRestException
     */
    public function delete(ConsumerCreateResponse $response) : void
    {
        $url = $response->baseUri();
        $clientRequest = $this->client->createRequest('DELETE', $url);

        $clientRequest = $clientRequest->withHeader('Content-Type', Client::TYPE_KAFKA);
        $this->client->sendRequest($clientRequest);
    }

    /**
     * @param Message[] $messages
     * @return ConsumerCommitOffsetsRequest
     */
    public function createConsumerCommitOffsetsRequest(array &$messages) : ConsumerCommitOffsetsRequest
    {
        $offsets = $this->getOffsetsForCommit($messages);
        $consumerCommitOffsetsRequest = new ConsumerCommitOffsetsRequest();
        foreach ($offsets as $topic => $partitions) {
            foreach ($partitions as $patition => $offset) {
                $consumerCommitOffsetsRequest->addOffset($topic, $patition, $offset);
            }
        }

        return $consumerCommitOffsetsRequest;
    }

    /**
     * @param Message[] $messages
     * @return array
     */
    private function getOffsetsForCommit(array &$messages) : array
    {
        $offsets = [];
        foreach ($messages as $message) {
            if (!isset($offsets[$message->topic()][$message->partition()])
                || $offsets[$message->topic()][$message->partition()] < $message->offset()
            ) {
                $offsets[$message->topic()][$message->partition()] = $message->offset();
            }
        }

        return $offsets;
    }
}