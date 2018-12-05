<?php

namespace LireinCore\KafkaRestClient;

use LireinCore\KafkaRestClient\Request\SendMessagesRequest;
use LireinCore\KafkaRestClient\Response\SendMessagesResponse;

class Producer
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
     * @param SendMessagesRequest $request
     * @return SendMessagesResponse
     * @throws KafkaRestException
     */
    public function send(SendMessagesRequest $request) : SendMessagesResponse
    {
        $topic = $request->topic();
        $url = $this->client->restHost() . "/topics/{$topic}";
        $clientRequest = $this->client->createRequest('POST', $url);

        $clientRequest = $clientRequest->withHeader('Accept', Client::TYPE_KAFKA);
        $clientRequest = $clientRequest->withHeader('Content-Type', $request->contentTypeHeader());
        $content = json_encode($request->body());
        $stream = $this->client->createStream($content);
        $clientRequest = $clientRequest->withBody($stream);
        $result = $this->client->sendRequest($clientRequest);

        $offsets = [];
        foreach ($result['offsets'] as $offset) {
            $offsets[] = new Offset(
                $offset['partition'] ?? null,
                $offset['offset'] ?? null,
                $offset['error_code'] ?? null,
                $offset['error'] ?? null
            );
        }
        $response = new SendMessagesResponse($result['key_schema_id'], $result['value_schema_id'], $offsets);

        return $response;
    }
}