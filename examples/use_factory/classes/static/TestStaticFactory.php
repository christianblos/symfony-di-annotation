<?php

namespace Example\UseFactory;

class TestStaticFactory
{
    public static function create()
    {
        return new TestStaticService('static');
    }
}
