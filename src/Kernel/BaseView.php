<?php

namespace Pano\Kernel;

use InvalidArgumentException;
use RuntimeException;
use Stringable;
use Throwable;

abstract class BaseView
{
    protected string $basePath;

    protected array $data = [];

    protected ?string $layout = null;

    protected array $sections = [];

    protected ?string $currentSection = null;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    public function with(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function layout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function render(string $view): string
    {
        $this->sections = [];
        $this->currentSection = null;

        $layout = $this->layout;
        $this->layout = null;

        $content = $this->capture(function () use ($view) {
            extract($this->data, EXTR_SKIP);

            require $this->resolve($view);
        });

        if ($layout !== null) {
            $this->sections['content'] = $content;

            $content = $this->capture(function () use ($layout) {
                extract($this->data, EXTR_SKIP);

                require $this->resolve($layout);
            });
        }

        return $content;
    }

    protected function resolve(string $view): string
    {
        $file = $this->basePath
            . DIRECTORY_SEPARATOR
            . ltrim($view, '/\\')
            . '.php';

        $path = realpath($file);

        if ($path === false || !is_file($path)) {
            throw new RuntimeException(
                sprintf('View [%s] not found.', $view)
            );
        }

        $basePath = realpath($this->basePath);

        if (
            $basePath === false ||
            !str_starts_with($path, $basePath)
        ) {
            throw new RuntimeException(
                sprintf('Invalid view path [%s].', $view)
            );
        }

        return $path;
    }

    protected function capture(callable $callback): string
    {
        ob_start();

        try {
            $callback();

            return ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }
    }

    public function start(string $name): void
    {
        if ($this->currentSection !== null) {
            throw new RuntimeException(
                sprintf(
                    'Nested sections are not supported. Close section [%s] before starting [%s].',
                    $this->currentSection,
                    $name
                )
            );
        }

        $this->currentSection = $name;

        ob_start();
    }

    public function end(): void
    {
        if ($this->currentSection === null) {
            throw new RuntimeException(
                'Cannot end a section that was never started.'
            );
        }

        $this->sections[$this->currentSection] = ob_get_clean();

        $this->currentSection = null;
    }

    public function section(string $name, string $default = ''): void
    {
        echo $this->sections[$name] ?? $default;
    }

    public function fragment(string $view, array $data = []): void
    {
        extract(
            array_merge($this->data, $data),
            EXTR_SKIP
        );

        require $this->resolve($view);
    }

    public function e(mixed $value): string
    {
        if (
            is_object($value) &&
            !$value instanceof Stringable
        ) {
            throw new InvalidArgumentException(
                'Value must be stringable.'
            );
        }

        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}