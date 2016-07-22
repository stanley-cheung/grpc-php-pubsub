<?php
require_once 'vendor/autoload.php';
require_once 'empty.php';
require_once 'pubsub.php';
require_once 'timestamp.php';
require_once 'descriptor.php';
require_once 'http.php';
require_once 'annotations.php';
use Google\Auth\ApplicationDefaultCredentials;

class PubsubSample {
  function updateAuthMetadataCallback($context)
  {
    $auth_credentials = ApplicationDefaultCredentials::getCredentials();
    return $auth_credentials->updateMetadata($metadata = [],
                                             $context->service_url);
  }

  function main() {
    $credentials = Grpc\ChannelCredentials::createComposite(
      Grpc\ChannelCredentials::createSsl(),
      Grpc\CallCredentials::createFromPlugin(
        [$this, 'updateAuthMetadataCallback'])
    );

    $client = new google\pubsub\v1\PublisherClient(
      'pubsub.googleapis.com',
      [
        'credentials' => $credentials,
        'grpc.ssl_target_name_override' => 'pubsub.googleapis.com'
      ]
    );

    $topic = new google\pubsub\v1\Topic();
    $topic->setName('projects/grpc-testing/topics/bla2');
    $call = $client->CreateTopic($topic);
    list($response, $status) = $call->wait();
    
    $req = new google\pubsub\v1\ListTopicsRequest();
    $req->setProject('projects/grpc-testing');

    $call = $client->ListTopics($req);
    list($response, $status) = $call->wait();

    $topics = $response->getTopics();
    foreach ($topics as $topic) {
      print("topic name = ".$topic->getName()."\n");
    }
  }
}

$sample = new PubsubSample();
$sample->main();
