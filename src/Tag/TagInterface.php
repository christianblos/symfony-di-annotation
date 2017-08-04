<?php

namespace Symfony\Component\DependencyInjection\Annotation\Tag;

interface TagInterface
{
    /**
     * @return string
     */
    public function getTagName();

    /**
     * @return array
     */
    public function getTagAttributes();
}
