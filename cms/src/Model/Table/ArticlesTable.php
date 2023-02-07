<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

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

    /**
     * Adds entity before save logic.
     *
     * For now, creates a slug based on the article's
     * title.
     *
     * @param EventInterface $event
     * @param mixed $entity
     * @param mixed $options
     * @return void
     */
    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    /**
     * Sets default validation rules when creating/editing Articles.
     *
     * @param Validator $validator Validation rules for form fields.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->notEmptyString('body')
            ->minLength('body', 10);

        return $validator;
    }
}
