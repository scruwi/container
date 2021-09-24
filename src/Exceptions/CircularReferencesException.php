<?php

namespace Scruwi\Container\Exceptions;

class CircularReferencesException extends ContainerException
{
    protected $message = 'Circular referenced in container';

    private array $path;

    public function __construct(array $path)
    {
        $this->path = $path;
        parent::__construct();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPath(): array
    {
        return $this->path;
    }
}
