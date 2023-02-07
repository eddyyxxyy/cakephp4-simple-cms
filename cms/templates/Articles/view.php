<h1><?php echo h($article->title); ?></h1>
<p><?php echo h($article->body); ?></p>
<p><b>Tags: </b><?php echo h($article->tag_string); ?></p>
<p><small>Created: <?php echo $article->created->format(DATE_RFC850); ?></small></p>
<p><?php echo $this->Html->link('Edit', ['action' => 'edit', $article->slug]); ?></p>
