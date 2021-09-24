<?php

namespace Tests\Autowired;

class CircularReferenceClass2
{
    public function __construct(CircularReferenceClass3 $instance)
    {
    }
}
