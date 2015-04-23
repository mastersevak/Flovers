<?//$this->renderPartial('news/_small-banner');?>
<?//$this->renderPartial('news/_results', compact('model')); //результаты поиска ?>

<div class="row">

	<div class="posts-isotope">
		<?$this->renderPartial('news/_list', compact('model', 'viewType'));?>
	</div>

</div>