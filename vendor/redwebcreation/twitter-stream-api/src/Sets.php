<?php

namespace RWC\TwitterStream;

class Sets
{
    protected array $sets;

    public function __construct(Fieldset ...$sets)
    {
        $this->sets = $sets;
    }

    public function getSets(): array
    {
        return $this->sets;
    }

    public function __toString(): string
    {
        return rtrim(array_reduce($this->sets, static function ($_, Fieldset $fieldset) {
            return $_ . $fieldset . '&';
        }, ''), '&');
    }
}
