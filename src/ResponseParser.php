<?php

declare(strict_types=1);

namespace Abyssale;

use Abyssale\Exception\RequestException;
use Abyssale\Result\GenerationRequest;
use Abyssale\Result\GenerationRequestInterface;
use Abyssale\Result\Image;
use Abyssale\Result\Pdf;

class ResponseParser
{
    /**
     * @throws RequestException When response is malformed
     */
    public function parseImageResponse(array $resultItem): Image
    {
        if (!array_key_exists('id', $resultItem)
            || !array_key_exists('image', $resultItem)
            || !array_key_exists('type', $resultItem['image'])
            || !array_key_exists('url', $resultItem['image'])
            || !array_key_exists('cdn_url', $resultItem['image'])
        ) {
            throw new RequestException('Malformed response from Abyssale');
        }

        return new Image(
            $resultItem['id'],
            $resultItem['image']['type'],
            $resultItem['image']['url'],
            $resultItem['image']['cdn_url'],
        );
    }

    /**
     * @throws RequestException When response is malformed
     */
    public function parsePdfResponse(array $resultItem): Pdf
    {
        if (!array_key_exists('id', $resultItem)
            || !array_key_exists('image', $resultItem)
            || !array_key_exists('url', $resultItem['image'])
            || !array_key_exists('cdn_url', $resultItem['image'])
        ) {
            throw new RequestException('Malformed response from Abyssale');
        }

        return new Pdf(
            $resultItem['id'],
            $resultItem['image']['url'],
            $resultItem['image']['cdn_url'],
        );
    }

    /**
     * @throws RequestException When response is malformed
     */
    public function parseAsyncResponse(array $resultItem): GenerationRequestInterface
    {
        if (!array_key_exists('generation_request_id', $resultItem)) {
            throw new RequestException('Malformed response from Abyssale');
        }

        return new GenerationRequest($resultItem['generation_request_id']);
    }
}
