<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Controller (handler) for /articles requests.
 */
class ArticlesController extends AppController
{
    public $paginate = [
        'className' => 'Simple',
    ];

    /**
     * Initializes code like loading components.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * Main action for /articles.
     *
     * It paginates over all Articles found.
     *
     * @return void
     */
    public function index() : void
    {
        $articles = $this->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    /**
     * Single article view.
     *
     * It handles the request from clicking at the
     * name of the post.
     *
     * @param string|null $slug
     * @return void
     */
    public function view(?string $slug = null) : void
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();

        $this->set(compact('article'));
    }

    /**
     * Handles post requests to add an article.
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            $article->user_id = 1;

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $tags = $this->Articles->Tags->find('list')->all();
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    /**
     * Handles patch/post/put requests to edit an article.
     *
     * @param string $slug Article identifier
     * @return void
     */
    public function edit($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }
        $tags = $this->Articles->Tags->find('list')->all();
        $this->set(compact('tags'));
        $this->set(compact('article'));
    }

    /**
     * Handles post/delete requests to delete the specified article.
     *
     * @param string $slug Article identifier.
     * @return void
     */
    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Get articles based on the tags from `pass` param.
     *
     * @return void
     */
    public function tags()
    {
        $tags = $this->request->getParam('pass');
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
            ])
            ->all();

        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}
