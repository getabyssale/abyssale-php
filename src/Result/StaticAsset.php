<?php

declare(strict_types=1);

namespace Abyssale\Result;

abstract class StaticAsset implements StaticAssetInterface
{
    protected string $id;

    protected string $url;

    protected string $cdnUrl;

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCdnUrl(): string
    {
        return $this->cdnUrl;
    }
}
