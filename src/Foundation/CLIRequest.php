<?php

namespace Pano\Foundation;

use Pano\Core\BaseRequest;
use Pano\Enum\HttpMethod;

final class CLIRequest extends BaseRequest
{

    public function __construct(array $data)
    {
        $this->fetchMethod()
            ->fetchOptions($data)
            ->fetchPath($data)
            ->fetchSegments($data)
            ->fetchCommand()
            ->fetchPositional($data)
            ->fetchFiles()
            ->fetchQueries();
    }

    public function getModule(): string
    {
        return $this->segments[0];
    }
    public function getPositional(): string|array
    {
        return $this->data;
    }

    public function getOptions(): array
    {
        return $this->headers;
    }

    public function getCommand(): string
    {
        return $this->url;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getPath(): string
    {
        return $this->host;
    }

    private function fetchMethod(): self
    {
        $this->method = HttpMethod::CLI;
        return $this;
    }

    /**
     * option arguments
     */
    private function fetchOptions(array $data): self
    {
        $parameters = [];
        foreach ($data as $arg) {
            if (str_starts_with($arg, '--')) {
                $parts = explode('=', substr($arg, 2), 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    $parameters[$key] = $value;
                } else {
                    $key = trim(substr($arg, 2));
                    $parameters[$key] = true;
                }
            }
        }
        $this->headers = $parameters;
        return $this;
    }

    private function fetchPath(array $data): self
    {
        $this->host = $data[0] . ' ' . $data[1];
        return $this;
    }

    private function fetchSegments(array $data): self
    {
        $this->segments = explode('/', $data[1]);
        return $this;
    }

    private function fetchCommand(): self
    {
        $uriSections = $this->segments;
        unset($uriSections[0]);
        $this->url = $uriSections[1];
        return $this;
    }

    /**
     * positional arguments
     */
    private function fetchPositional(array $data): self
    {
        $arguments = array_slice($data, 2);
        foreach ($arguments as $argument) {
            if (!str_starts_with($argument, '--')) {
                $this->data[] = $argument;
            }
        }
        return $this;
    }

    private function fetchFiles(): self
    {
        $this->files = [];
        return $this;
    }

    private function fetchQueries(): void
    {
        $this->queries = [];
    }


    public function expectsJson(): bool
    {
        return false;
    }

}