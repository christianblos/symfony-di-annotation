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
     */
    public $value;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $keyConst;

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
            'mapTo'    => $this->value,
            'key'      => $this->key,
            'keyConst' => $this->keyConst,
        ];
    }
}
