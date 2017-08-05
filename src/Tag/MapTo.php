<?php

namespace Symfony\Component\DependencyInjection\Annotation\Tag;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class MapTo implements TagInterface
{
    const TAG_NAME = 'mapTo';

    /**
     * @var string
     * @Required
     */
    public $value;

    /**
     * @var string
     * @Required
     */
    public $key;

    /**
     * @return string
     */
    public function getTagName()
    {
        return self::TAG_NAME;
    }

    /**
     * @return array
     */
    public function getTagAttributes()
    {
        return [
            'mapTo' => $this->value,
            'key'   => $this->key,
        ];
    }
}
