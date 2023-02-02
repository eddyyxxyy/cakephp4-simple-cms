<?php

namespace App\Controller;

/**
 * Controller (handler) for /articles requests.
 */
class ArticlesController extends AppController
{
    /**
     * Main action for /articles.
     *
     * It paginates over all Articles found.
     *
     * @return void
     */
    public function index()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }
}
