<?php

declare(strict_types=1);

namespace Abyssale\Exception;

class NotEnoughCreditsException extends ClientException
{
    public function __construct()
    {
        parent::__construct(
            "You don't have enough credits to perform this action. Upgrade your plan.",
            429
        );
    }
}