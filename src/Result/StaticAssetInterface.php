<?php

declare(strict_types=1);

namespace Abyssale\Result;

interface StaticAssetInterface extends EntityInterface
{
    public function getUrl(): string;

    public function getCdnUrl(): string;
}
