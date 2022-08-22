<?php

declare(strict_types=1);

namespace Abyssale\Result;

class Pdf extends StaticAsset implements PdfInterfaceStatic
{
    public function __construct(string $id, string $url, string $cdnUrl)
    {
        $this->id = $id;
        $this->url = $url;
        $this->cdnUrl = $cdnUrl;
    }
}
