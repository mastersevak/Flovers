<div id="top-filters" class="top-filters mb10 {$this->class}" data-ajax-update="{$ajaxUpdate}" data-model="{get_class($this->model)}">
	<div class="filter-tools pull-right">
		<a href="#" class="fa fa-minus-square btn-toggle-filters" rel="tooltip" title="Скрыть фильтры"></a>
		<a href="#" class="fa fa-trash-o btn-clear-filters" rel="tooltip" title="Очистить фильтры"></a> 
		<a href="#" class="fa fa-eye btn-show-hidden-filters" rel="tooltip" title="Показать блоки" data-toggle="dropdown"></a>
		<ul class="dropdown-menu">{$this->renderHiddenBlocks()}</ul>
	</div>

	<div class="filter-blocks clearfix">
		{$content}
	</div>
	
	<a href="#" class="btn-submit btn-success search btn fr mr5 c-white">Поиск</a>
	<br clear="both">
</div>