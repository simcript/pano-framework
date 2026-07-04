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

    public function setModule(null|string $module = null): static
    {
        if ($module !== null) {
            $this->module = $module;
        } else if (config('app.resolver') === 'subdomain') {
            $host = parse_url($this->host, PHP_URL_HOST);
            $rootDomain = parse_url(config('app.url'), PHP_URL_HOST);

            if ((empty($host) || empty($rootDomain))
                || (($host === $rootDomain)
                    || !str_ends_with($host, '.' . $rootDomain))) {
                $this->module = '';
            } else {
                $this->module = rtrim(
                    substr($host, 0, -strlen($rootDomain)),
                    '.'
                );
            }
        } else {
            $this->module = $this->segments[0] ?? '';
        }

        return $this;
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

}