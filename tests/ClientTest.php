<?php

declare(strict_types=1);

use Abyssale\Client;
use Abyssale\ResponseParser;
use Abyssale\Result\GenerationRequestInterface;
use Abyssale\Result\Image;
use Abyssale\Result\Pdf;
use GuzzleHttp\Psr7\Response;
use Http\Message\RequestMatcher\RequestMatcher;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public const TEMPLATE_UUID = '43c13fd4-1070-4514-9154-1363ecb4e440';

    private Client $client;
    private \Http\Mock\Client $httpClient;

    /**
     * @var ResponseParser|\PHPUnit\Framework\MockObject\MockObject
     */
    private $parserMock;

    protected function setUp(): void
    {
        $this->httpClient = new \Http\Mock\Client();
        $this->parserMock = $this->createMock(ResponseParser::class);
        $this->client = new Client('api_key', $this->httpClient, null, $this->parserMock);
    }

    public function testImageGenerationSuccess(): void
    {
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(200, [], json_encode(['id' => 'abc']))
        );

        $resultImage = $this->createMock(Image::class);
        $this->parserMock->method('parseImageResponse')->willReturn($resultImage);

        self::assertEquals($this->client->generateImage(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
            [],
        ), $resultImage);
    }

    public function testImageGenerationThrowExceptionOn4xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ClientException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(400)
        );

        $response = $this->client->generateImage(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
            [],
        );
    }

    public function testImageGenerationThrowExceptionOn5xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ServerException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(500)
        );

        $response = $this->client->generateImage(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
            [],
        );
    }

    public function testImageGenerationThrowExceptionOnRandomException(): void
    {
        $this->expectException(\Abyssale\Exception\RequestException::class);
        $this->httpClient->addResponse(new Response(200, [], json_encode(['id' => 'abc'])));
        $this->parserMock->method('parseImageResponse')->willThrowException(new \RuntimeException());

        $response = $this->client->generateImage(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
            [],
        );
    }

    public function testPdfGenerationSuccess(): void
    {
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(200, [], json_encode(['id' => 'abc']))
        );

        $resultPdf = $this->createMock(Pdf::class);
        $this->parserMock->method('parsePdfResponse')->willReturn($resultPdf);

        self::assertEquals($this->client->generatePdf(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
        ), $resultPdf);
    }

    public function testPdfGenerationThrowExceptionOn4xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ClientException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(400)
        );

        $response = $this->client->generatePdf(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
        );
    }

    public function testPdfGenerationThrowExceptionOn5xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ServerException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(500)
        );

        $response = $this->client->generatePdf(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
        );
    }

    public function testPdfGenerationThrowExceptionOnRandomException(): void
    {
        $this->expectException(\Abyssale\Exception\RequestException::class);
        $this->httpClient->addResponse(new Response(200, [], json_encode(['id' => 'abc'])));
        $this->parserMock->method('parsePdfResponse')->willThrowException(new \RuntimeException());

        $response = $this->client->generatePdf(
            self::TEMPLATE_UUID,
            'instagram-story',
            [],
        );
    }

    public function testAsyncImageGenerationSuccess(): void
    {
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(200, [], json_encode(['id' => 'abc']))
        );

        $result = $this->createMock(GenerationRequestInterface::class);
        $this->parserMock->method('parseAsyncResponse')->willReturn($result);

        self::assertEquals($this->client->asyncGenerateImage(
            self::TEMPLATE_UUID,
            ['instagram-story', 'linkedin-feed'],
            [],
            'https://mywebhook.com/123',
            [],
        ), $result);
    }

    public function testAsyncImageGenerationThrowExceptionOn4xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ClientException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(400)
        );

        $response = $this->client->asyncGenerateImage(
            self::TEMPLATE_UUID,
            ['instagram-story', 'linkedin-feed'],
            [],
            'https://mywebhook.com/123',
            [],
        );
    }

    public function testAsyncImageGenerationThrowExceptionOn5xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ServerException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(500)
        );

        $response = $this->client->asyncGenerateImage(
            self::TEMPLATE_UUID,
            ['instagram-story', 'linkedin-feed'],
            [],
            'https://mywebhook.com/123',
            [],
        );
    }

    public function testAsyncImageGenerationThrowExceptionOnRandomException(): void
    {
        $this->expectException(\Abyssale\Exception\RequestException::class);
        $this->httpClient->addResponse(new Response(200));
        $this->parserMock->method('parseAsyncResponse')->willThrowException(new \RuntimeException());

        $response = $this->client->asyncGenerateImage(
            self::TEMPLATE_UUID,
            ['instagram-story', 'linkedin-feed'],
            [],
            'https://mywebhook.com/123',
            [],
        );
    }

    public function testAsyncPdfGenerationSuccess(): void
    {
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(200, [], json_encode(['id' => 'abc']))
        );

        $result = $this->createMock(GenerationRequestInterface::class);
        $this->parserMock->method('parseAsyncResponse')->willReturn($result);

        self::assertEquals($this->client->asyncGeneratePdf(
            self::TEMPLATE_UUID,
            ['a4', 'a3'],
            [],
            'https://mywebhook.com/123',
        ), $result);
    }

    public function testAsyncPdfGenerationThrowExceptionOn4xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ClientException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(400)
        );

        $response = $this->client->asyncGeneratePdf(
            self::TEMPLATE_UUID,
            ['a4', 'a3'],
            [],
            'https://mywebhook.com/123',
        );
    }

    public function testAsyncPdfGenerationThrowExceptionOn5xxResponse(): void
    {
        $this->expectException(\Abyssale\Exception\ServerException::class);
        $this->httpClient->on(
            new RequestMatcher(sprintf('/async/banner-builder/%s/generate', self::TEMPLATE_UUID)),
            new Response(500)
        );

        $response = $this->client->asyncGeneratePdf(
            self::TEMPLATE_UUID,
            ['a4', 'a3'],
            [],
            'https://mywebhook.com/123',
        );
    }

    public function testAsyncPdfGenerationThrowExceptionOnRandomException(): void
    {
        $this->expectException(\Abyssale\Exception\RequestException::class);
        $this->httpClient->addResponse(new Response(200));
        $this->parserMock->method('parseAsyncResponse')->willThrowException(new \RuntimeException());

        $response = $this->client->asyncGeneratePdf(
            self::TEMPLATE_UUID,
            ['a4', 'a3'],
            [],
            'https://mywebhook.com/123',
            [],
        );
    }
}
