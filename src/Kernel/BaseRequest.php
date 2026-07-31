<?php

namespace Pano\Kernel;

abstract class BaseRequest
{
    protected string|array $data = [];
    protected array $files = [];
    protected array $headers = [];
    protected array $queries = [];
    protected HttpMethodEnum $method = HttpMethodEnum::GET;
    protected string $query = '';
    protected string $url = '';
    protected string $module = '';
    protected array $segments = [];
    protected string $host = '';
    public BaseBag $attributes;

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

    public function getMethod(): HttpMethodEnum
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

    public function getModule(): string
    {
        return $this->module;
    }

}