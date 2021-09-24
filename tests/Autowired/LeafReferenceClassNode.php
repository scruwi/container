<?php

namespace Tests\Autowired;

class LeafReferenceClassNode
{
    public function __construct(LeafReferenceClassLeaf1 $leaf1, LeafReferenceClassLeaf2 $leaf2)
    {
    }
}
