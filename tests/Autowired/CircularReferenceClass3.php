<?php

namespace Tests\Autowired;

class CircularReferenceClass3
{
    public function __construct(CircularReferenceClass1 $instance)
    {
    }
}
