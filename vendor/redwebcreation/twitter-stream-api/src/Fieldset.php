<?php

namespace RWC\TwitterStream;

class Fieldset
{
    protected string $name;
    protected array $fields;

    /** The name of the query parameter (like user.fields, expansions...) */
    public function __construct(string $name, string ...$fields)
    {
        $this->name   = $name;
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function __toString(): string
    {
        return $this->name . '=' . implode(',', $this->fields);
    }
}
