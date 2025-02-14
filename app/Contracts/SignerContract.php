<?php

declare(strict_types=1);

namespace App\Contracts;

interface SignerContract
{
    public function sign(array $payload): string;

    public function validate(string $signature, array $payload): bool;
}
