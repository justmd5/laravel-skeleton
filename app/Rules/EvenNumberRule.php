<?php

namespace App\Rules;

class EvenNumberRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^\d*[02468]$/';
    }
}
