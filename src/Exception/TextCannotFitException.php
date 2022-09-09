<?php

declare(strict_types=1);

namespace Abyssale\Exception;

class TextCannotFitException extends ClientException
{
    private const PATTERN = "#Element (.*) error: The text '(.*)' cannot fit within the defined space.#";

    private string $layer;

    private string $providedText;

    public static function match($message): bool
    {
        return preg_match(self::PATTERN, $message) === 1;
    }

    public static function fromMessage($message): self
    {
        preg_match(self::PATTERN, $message, $matches);

        $layer = $matches[1];
        $text = $matches[2];

        $e = new TextCannotFitException(
            sprintf(
                'Text "%s" cannot fit in the bounding box for layer "%s"',
                $text,
                $layer,
            ),
            400
        );
        $e->setLayer($layer);
        $e->setProvidedText($text);

        return $e;
    }

    public function getLayer(): string
    {
        return $this->layer;
    }

    public function setLayer(string $layer): void
    {
        $this->layer = $layer;
    }

    public function getProvidedText(): string
    {
        return $this->providedText;
    }

    public function setProvidedText(string $providedText): void
    {
        $this->providedText = $providedText;
    }
}