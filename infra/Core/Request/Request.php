<?php

namespace Infra\Core\Request;

class Request
{
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->request[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->request[$key] = $value;
    }

    public function all(): array
    {
        return $this->request;
    }
}
