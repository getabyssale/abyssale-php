<?php

declare(strict_types=1);

namespace Abyssale;

use Abyssale\Result\GenerationRequestInterface;
use Abyssale\Result\Image;
use Abyssale\Result\Pdf;

interface ClientInterface
{
    /**
     * Generate a static image from a template
     *
     * The options:
     * - image_type: string. `jpeg` or `png`. Default to Abyssale logic.
     * - compression_level: int between 1 and 100. Level of compression applied to the result image. Default is 95.
     *
     * @param string      $templateId ID of the template
     * @param string|null $formatName Name of the format you want to generate. Use `null` for default format.
     * @param array       $elements   Elements that will override the template layers.
     * @param array       $options    Custom options about the result image.
     */
    public function generateImage(string $templateId, ?string $formatName, array $elements = [], array $options = []): Image;

    /**
     * Generate a pdf from a template
     *
     * @param string      $templateId ID of the template
     * @param string|null $formatName Name of the format you want to generate. Use `null` for default format.
     * @param array       $elements   Elements that will override the template layers.
     */
    public function generatePdf(string $templateId, ?string $formatName, array $elements = []): Pdf;

    /**
     * Trigger an asynchronous static image generation from a template
     *
     * The options:
     * - image_type: string. `jpeg` or `png`. Default to Abyssale logic.
     * - compression_level: int between 1 and 100. Level of compression applied to the result image. Default is 95.
     *
     * @param string      $templateId  ID of the template
     * @param array|null  $formatNames Names of the formats you want to generate. Use `null` for default format.
     * @param array       $elements    Elements that will override the template layers.
     * @param string|null $callbackUrl Url of the webhook that will be called when the image is ready.
     * @param array       $options     Custom options about the result image.
     */
    public function asyncGenerateImage(string $templateId, ?array $formatNames, array $elements = [], ?string $callbackUrl = null, array $options = []): GenerationRequestInterface;

    /**
     * Trigger an asynchronous pdf generation from a template
     *
     * @param string      $templateId  ID of the template
     * @param array|null  $formatNames Names of the formats you want to generate. Use `null` for default format.
     * @param array       $elements    Elements that will override the template layers.
     * @param string|null $callbackUrl Url of the webhook that will be called when the image is ready.
     */
    public function asyncGeneratePdf(string $templateId, ?array $formatNames, array $elements = [], ?string $callbackUrl = null): GenerationRequestInterface;

//
//    public function asyncGeneratePdf($templateId): GenerationRequestInterface;
//
}
