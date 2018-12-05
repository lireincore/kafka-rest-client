# Kafka rest client

[![Latest Stable Version](https://poser.pugx.org/lireincore/imgcache/v/stable)](https://packagist.org/packages/lireincore/imgcache)
[![Total Downloads](https://poser.pugx.org/lireincore/imgcache/downloads)](https://packagist.org/packages/lireincore/imgcache)
[![License](https://poser.pugx.org/lireincore/imgcache/license)](https://packagist.org/packages/lireincore/imgcache)

## About

Kafka rest client for confluent rest proxy

## Install

Add the `"lireincore/kafka-rest-proxy": "dev-master"` package to your `require` section in the `composer.json` file

or

``` bash
$ php composer.phar require lireincore/imgcache dev-master
```

## Usage

```php
use Psr\Log\LoggerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use LireinCore\KafkaRestClient\Client;
use LireinCore\KafkaRestClient\Producer;
use LireinCore\KafkaRestClient\Consumer;
use LireinCore\KafkaRestClient\KafkaRestException;
use LireinCore\KafkaRestClient\Request\SendMessagesRequest;

//$client implements Psr\Http\Client\ClientInterface
//$requestFactory implements Psr\Http\Message\RequestFactoryInterface
//$streamFactory implements Psr\Http\Message\StreamFactoryInterface
//$logger implements Psr\Log\LoggerInterface
$kafkaClient = new Client($client, $requestFactory, $streamFactory, 'rest-host:8082', null, $logger);

//produce message
$producer = new Producer($kafkaClient);
$request = (new SendMessagesRequest('test_topic'))
    ->addRecord('test value');
$response = $producer->send($request);

/***************************************************************/
//consume message
$consumer = new Consumer($kafkaClient);
$consumerCreateRequest = new ConsumerCreateRequest('test_group');
$consumerCreateResponse = $consumer->create($consumerCreateRequest);

$consumerAssignmentRequest = (new ConsumerAssignmentRequest())
    ->addPartition('test_topic', 0);
$consumer->assign($consumerAssignmentRequest, $consumerCreateResponse);

$getMessagesRequest = new GetMessagesRequest();
$messages = $consumer->pool($getMessagesRequest, $consumerCreateResponse);

if ($messages) {
    //...custom process messages
    //commit last offsets
    $consumerCommitOffsetsRequest = $consumer->createConsumerCommitOffsetsRequest($messages);
    try {
        $consumer->commit($consumerCommitOffsetsRequest, $consumerCreateResponse);
    } catch (KafkaRestException $ex) {
        var_dump($ex->getMessage());
    }
}

$consumer->delete($consumerCreateResponse);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.