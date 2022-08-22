<?php

declare(strict_types=1);

namespace Abyssale\Result;

class GenerationRequest implements GenerationRequestInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
