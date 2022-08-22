<?php

declare(strict_types=1);

namespace Abyssale\Result;

interface ImageInterface extends StaticAssetInterface
{
    public function getFileType(): string;
}
