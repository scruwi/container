<?php

namespace Tests\Autowired;

class ClassWithScalarParameters
{
    public bool $bool;

    public string $string;

    public string $default;

    public function __construct(bool $bool, string $string, $default = 'default')
    {
        $this->bool = $bool;
        $this->string = $string;
        $this->default = $default;
    }
}
