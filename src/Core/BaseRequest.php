<?php

namespace Pano\Core;

use Pano\Enum\HttpMethod;

abstract class BaseRequest
{
    protected string|array $data;
    protected array $files;
    protected array $headers;
    protected array $queries;
    protected HttpMethod $method;
    protected string $query;
    protected string $url;
    protected array $segments;
    protected string $host;

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