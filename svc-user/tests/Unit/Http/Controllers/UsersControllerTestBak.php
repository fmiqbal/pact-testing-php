<?php

namespace Http\Controllers;

use GuzzleHttp;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use Tests\TestCase;
use function json_decode;

class UsersControllerTestBak extends TestCase
{
    public function httpClient($baseUri, string $uri)
    {
        $httpClient = new GuzzleHttp\Client();
        $response = $httpClient->get(new Uri("{$baseUri}/{$uri}"), [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $body = $response->getBody();

        return json_decode($body);
    }

    public function testIndex()
    {
        $client = new GuzzleHttp\Client();

        try {
            $client->delete('localhost:7200/interactions', [
                'headers' => [
                    'X-Pact-Mock-Service' => 'true',
                ],
            ]);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
        }
        $matcher = new Matcher();

        // Create your expected request from the consumer.
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/users')
            ->addHeader('Content-Type', 'application/json');

        // Create your expected response from the provider.
        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody(
                $matcher->eachLike([
                    'id' => $matcher->integer(1),
                ]),
            );

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('Get all users')
            ->with($request)
            ->willRespondWith(
                $response
            ); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.

        $result = $this->httpClient($config->getBaseUri(), 'users'); // Pass in the URL to the Mock Server.

        $builder->verify(); // This will verify that the interactions took place.

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'id' => 1,
                ],
            ]),
            json_encode($result)
        ); // Make your assertions.

        $client->post('localhost:7200/pact', [
            'headers' => [
                'X-Pact-Mock-Service' => 'true',
            ],
        ]);
    }
}
