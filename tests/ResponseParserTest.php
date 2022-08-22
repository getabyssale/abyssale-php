<?php

declare(strict_types=1);

use Abyssale\Exception\RequestException;
use Abyssale\ResponseParser;
use Abyssale\Result\GenerationRequestInterface;
use Abyssale\Result\Image;
use Abyssale\Result\Pdf;
use PHPUnit\Framework\TestCase;

final class ResponseParserTest extends TestCase
{
    private const UUID = '87e4cf30-f2cd-415a-ac1a-97c3799d9083';
    private const UUID2 = '10f48214-9c67-47d6-b6a0-e3602692d9e9';
    private const IMAGE_URL = 'https://test.io/123.png';
    private const CDN_IMAGE_URL = 'https://test.io/123.png';
    private const PDF_URL = 'https://test.io/123.pdf';
    private const CDN_PDF_URL = 'https://test.io/123.pdf';

    private ResponseParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ResponseParser();
    }

    public function testCantParseAnInvalidImageResponse(): void
    {
        $this->expectException(RequestException::class);
        $this->parser->parseImageResponse([]);
    }

    public function testCanParseAValidImageResponse(): void
    {
        self::assertInstanceOf(
            Image::class,
            $this->parser->parseImageResponse($this->getValidImageResponse())
        );
    }

    public function testImageHasCorrectId(): void
    {
        $image = $this->parser->parseImageResponse($this->getValidImageResponse());

        self::assertEquals(self::UUID, $image->getId());
    }

    public function testImageHasCorrectFileType(): void
    {
        $image = $this->parser->parseImageResponse($this->getValidImageResponse());

        self::assertEquals('png', $image->getFileType());
    }

    public function testImageHasCorrectUrl(): void
    {
        $image = $this->parser->parseImageResponse($this->getValidImageResponse());

        self::assertEquals(self::IMAGE_URL, $image->getUrl());
    }

    public function testImageHasCorrectCdnUrl(): void
    {
        $image = $this->parser->parseImageResponse($this->getValidImageResponse());

        self::assertEquals(self::CDN_IMAGE_URL, $image->getCdnUrl());
    }

    public function testCantParseAnInvalidPdfResponse(): void
    {
        $this->expectException(RequestException::class);
        $this->parser->parsePdfResponse([]);
    }

    public function testCanParseAValidPdfResponse(): void
    {
        self::assertInstanceOf(
            Pdf::class,
            $this->parser->parsePdfResponse($this->getValidPdfResponse())
        );
    }

    public function testPdfHasCorrectId(): void
    {
        $image = $this->parser->parsePdfResponse($this->getValidPdfResponse());

        self::assertEquals(self::UUID2, $image->getId());
    }

    public function testPdfHasCorrectUrl(): void
    {
        $image = $this->parser->parsePdfResponse($this->getValidPdfResponse());

        self::assertEquals(self::PDF_URL, $image->getUrl());
    }

    public function testPdfHasCorrectCdnUrl(): void
    {
        $image = $this->parser->parsePdfResponse($this->getValidPdfResponse());

        self::assertEquals(self::CDN_PDF_URL, $image->getCdnUrl());
    }

    public function testCantParseAnInvalidAsyncImageResponse(): void
    {
        $this->expectException(RequestException::class);
        $this->parser->parseImageResponse([]);
    }

    public function testCanParseAValidAsyncResponse(): void
    {
        self::assertInstanceOf(
            GenerationRequestInterface::class,
            $this->parser->parseAsyncResponse($this->getValidAsyncResponse())
        );
    }

    private function getValidImageResponse(): array
    {
        return [
            'id' => self::UUID,
            'image' => [
                'type' => 'png',
                'url' => self::IMAGE_URL,
                'cdn_url' => self::CDN_IMAGE_URL,
            ],
        ];
    }

    private function getValidPdfResponse(): array
    {
        return [
            'id' => self::UUID2,
            'image' => [
                'url' => self::PDF_URL,
                'cdn_url' => self::CDN_PDF_URL,
            ],
        ];
    }

    private function getValidAsyncResponse(): array
    {
        return [
            'generation_request_id' => self::UUID,
        ];
    }
}
