<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
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
        $this->belongsToMany('Tags', [
            'joinTable' => 'articles_tags',
            'dependent' => true
        ]);
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
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }
    }

    /**
     * Takes in `tagString` and filter it, then turn it unique.
     *
     * @param string $tagString
     * @return void
     */
    protected function _buildTags($tagString)
    {
        $newTags = array_map('trim', explode(',', $tagString));
        $newTags = array_filter($newTags);
        $newTags = array_unique($newTags);

        $out = [];
        $tags = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags])
            ->all();

        foreach ($tags->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        foreach ($tags as $tag) {
            $out[] = $tag;
        }
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }

        return $out;
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

    /**
     * Find specific articles based on tags.
     *
     * @param Query $query Query builder instance.
     * @param array $options Contain the 'tags' passed in find('tagged') in the controller.
     * @return void
     */
    public function findTagged(Query $query, array $options)
    {
        $columns = [
            'Articles.id', 'Articles.user_id', 'Articles.title',
            'Articles.body', 'Articles.published', 'Articles.created',
            'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }
}
