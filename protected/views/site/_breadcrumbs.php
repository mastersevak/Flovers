<?if($this->breadcrumbs)
	$this->widget('SBreadcrumbs',[
		'htmlOptions' => ['class' => 'breadcrumb mb20'],
		'links' => $this->breadcrumbs
	]);
?>
