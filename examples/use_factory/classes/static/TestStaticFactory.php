<?php

namespace Example\UseFactory;

class TestStaticFactory
{
    public static function create(): TestStaticService
    {
        return new TestStaticService('static');
    }
}
