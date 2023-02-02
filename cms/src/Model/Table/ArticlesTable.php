<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Represents `articles` table.
 */
class ArticlesTable extends Table
{
    /**
     * Behaviors and 'rules' are declared here.
     *
     * @param array $config Configuration params for the table.
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
    }
}
