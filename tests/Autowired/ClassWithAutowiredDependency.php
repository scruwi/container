<?php

namespace Tests\Autowired;

class ClassWithAutowiredDependency
{
    public function __construct(ClassWithoutDependencies $dependency)
    {
    }
}
