<?php

declare(strict_types=1);

namespace Abyssale\Result;

class Image extends StaticAsset implements ImageInterface
{
    /**
     * @var string "png"|"jpeg"
     */
    private string $fileType;

    public function __construct(string $id, string $fileType, string $url, string $cdnUrl)
    {
        $this->id = $id;
        $this->fileType = $fileType;
        $this->url = $url;
        $this->cdnUrl = $cdnUrl;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }
}
