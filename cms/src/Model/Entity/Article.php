<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Represents a row from `articles` table.
 */
class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false
    ];
}
