<?php

namespace App\Rules;

class CarNumberRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        return '/^[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}$/';
    }
}
