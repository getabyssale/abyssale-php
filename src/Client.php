<?php

declare(strict_types=1);

namespace Abyssale;

use Abyssale\Exception\ClientException;
use Abyssale\Exception\RequestException;
use Abyssale\Exception\ServerException;
use Abyssale\Result\GenerationRequestInterface;
use Abyssale\Result\Image;
use Abyssale\Result\Pdf;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Exception\ServerErrorException;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client implements ClientInterface
{
    public const ABYSSALE_URL = 'https://api.abyssale.com';

    private ResponseParser $responseParser;

    private HttpClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $apiKey;

    public function __construct(
        string $apiKey,
        ?HttpClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?ResponseParser $responseParser = null
    ) {
        $this->apiKey = $apiKey;
        $this->httpClient = new PluginClient($httpClient ?: HttpClientDiscovery::find(), [new ErrorPlugin()]);
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
        $this->responseParser = $responseParser ?: new ResponseParser();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientException  When Abyssale return a 4XX http code.
     * @throws ServerException  When Abyssale return a 5XX http code.
     * @throws RequestException When an unknown error occurs.
     */
    public function generateImage(string $templateId, ?string $formatName, array $elements = [], array $options = []): Image
    {
        $body = ['elements' => $elements];

        if ($formatName != null) {
            $body['template_format_name'] = $formatName;
        }

        if (array_key_exists('image_type', $options)) {
            $body['image_file_type'] = $options['image_type'];
        }

        if (array_key_exists('compression_level', $options)) {
            $body['file_compression_level'] = $options['compression_level'];
        }

        $request = $this->createRequest(sprintf('/banner-builder/%s/generate', $templateId), $body);

        return $this->handleRequest($request, function (ResponseParser $responseParser, array $jsonContent) {
            return $responseParser->parseImageResponse($jsonContent);
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientException  When Abyssale return a 4XX http code.
     * @throws ServerException  When Abyssale return a 5XX http code.
     * @throws RequestException When an unknown error occurs.
     */
    public function generatePdf(string $templateId, ?string $formatName, array $elements = []): Pdf
    {
        $body = [
            'elements' => $elements,
            'image_file_type' => 'pdf',
        ];

        if ($formatName != null) {
            $body['template_format_name'] = $formatName;
        }

        $request = $this->createRequest(sprintf('/banner-builder/%s/generate', $templateId), $body);

        return $this->handleRequest($request, function (ResponseParser $responseParser, array $jsonContent) {
            return $responseParser->parsePdfResponse($jsonContent);
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientException  When Abyssale return a 4XX http code.
     * @throws ServerException  When Abyssale return a 5XX http code.
     * @throws RequestException When an unknown error occurs.
     */
    public function asyncGenerateImage(string $templateId, ?array $formatNames, array $elements = [], ?string $callbackUrl = null, array $options = []): GenerationRequestInterface
    {
        $body = ['elements' => $elements];

        if ($callbackUrl !== null) {
            $body['callback_url'] = $callbackUrl;
        }

        if ($formatNames != null) {
            $body['template_format_names'] = $formatNames;
        }

        if (array_key_exists('image_type', $options)) {
            $body['image_file_type'] = $options['image_type'];
        }

        if (array_key_exists('compression_level', $options)) {
            $body['file_compression_level'] = $options['compression_level'];
        }

        $request = $this->createRequest(sprintf('/async/banner-builder/%s/generate', $templateId), $body);

        return $this->handleRequest($request, function (ResponseParser $responseParser, array $jsonContent) {
            return $responseParser->parseAsyncResponse($jsonContent);
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientException  When Abyssale return a 4XX http code.
     * @throws ServerException  When Abyssale return a 5XX http code.
     * @throws RequestException When an unknown error occurs.
     */
    public function asyncGeneratePdf(string $templateId, ?array $formatNames, array $elements = [], ?string $callbackUrl = null, array $options = []): GenerationRequestInterface
    {
        $body = [
            'elements' => $elements,
            'image_file_type' => 'pdf',
        ];

        if ($callbackUrl !== null) {
            $body['callback_url'] = $callbackUrl;
        }

        if ($formatNames != null) {
            $body['template_format_names'] = $formatNames;
        }

        $request = $this->createRequest(sprintf('/async/banner-builder/%s/generate', $templateId), $body);

        return $this->handleRequest($request, function (ResponseParser $responseParser, array $jsonContent) {
            return $responseParser->parseAsyncResponse($jsonContent);
        });
    }

    private function createRequest(string $path, array $body): RequestInterface
    {
        return $this->requestFactory
            ->createRequest('POST', sprintf('%s%s', self::ABYSSALE_URL, $path))
            ->withHeader('x-api-key', $this->apiKey)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($body)));
    }

    /**
     * @return mixed
     */
    private function handleRequest(RequestInterface $request, callable $parserCallback)
    {
        try {
            $response = $this->httpClient->sendRequest($request);
            $jsonContent = json_decode($response->getBody()->getContents(), true);

            return $parserCallback($this->responseParser, $jsonContent);
        } catch (ClientErrorException $e) {
            throw new ClientException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (ServerErrorException $e) {
            throw new ServerException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (\Throwable $e) {
            throw new RequestException("Unknown error during Abyssale API call", 0, $e);
        }
    }
}
