<?php
/* @var $this NewsController */
/* @var $model News */
$this->breadcrumbs = array(t('photoalbum', 'Фотоальбомы')=>array('index'), $this->pageTitle);
?>	

<?php echo $this->renderPartial('/album/_form', compact('model')); ?>	
