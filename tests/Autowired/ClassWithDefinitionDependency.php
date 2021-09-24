<?php

namespace Tests\Autowired;

class ClassWithDefinitionDependency
{
    private ClassCreatedByDefinition $dependency;

    public function __construct(ClassCreatedByDefinition $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassCreatedByDefinition
    {
        return $this->dependency;
    }
}
