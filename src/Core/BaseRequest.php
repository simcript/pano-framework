<?php

namespace Pano\Core;

use Pano\Enum\HttpMethod;
use Pano\Foundation\Bag;

abstract class BaseRequest
{
    protected readonly string|array $data;
    protected readonly array $files;
    protected readonly array $headers;
    protected readonly array $queries;
    protected readonly HttpMethod $method;
    protected readonly string $query;
    protected readonly string $url;
    protected readonly array $segments;
    protected readonly string $host;
    public Bag $attributes;

    public function getData(): string|array
    {
        return $this->data;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getHost(): string
    {
        return $this->host;
    }

}