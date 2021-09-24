<?php

namespace Scruwi\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{

}
