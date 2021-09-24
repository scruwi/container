<?php

namespace Tests\Autowired;

class CircularReferenceClass1
{
    public function __construct(CircularReferenceClass2 $instance)
    {
    }
}
