<?php

namespace Tests\Autowired;

class ClassWithInterfaceDependency
{
    public SomeInterface $dependency;

    public function __construct(SomeInterface $dependency)
    {
        $this->dependency = $dependency;
    }
}
