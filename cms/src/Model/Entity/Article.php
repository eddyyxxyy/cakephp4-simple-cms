<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;

/**
 * Represents a row from `articles` table.
 */
class Article extends Entity
{
    /**
     * Defines accessible data in our Entity.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
        'tag_string' => true,
    ];

    /**
     * Implements a simple way to access formatted tags.
     *
     * @return string
     */
    protected function _getTagString()
    {
        if (isset($this->_fields['tag_string'])) {
            return $this->_fields['tag_string'];
        }
        if (empty($this->tags)) {
            return '';
        }

        $tags = new Collection($this->tags);

        $str = $tags->reduce(function ($string, $tag) {
            return $string . $tag->title . ', ';
        }, '');

        return trim($str, ', ');
    }
}
