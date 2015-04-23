<?
	$criteria = isset($criteria) ? $criteria : false;
	$dataProvider = isset($dataProvider) ? $dataProvider : $model->frontSearch($criteria);
	$class = Cookie::get('productListView') ?  Cookie::get('productListView') : 'products-list-in-column';
?>

<?$this->widget('EListView', [
	'id' => "products-list",
	'dataProvider' => $dataProvider,
	'itemView' => 'products/'.$itemView,
	'ajaxUpdate' => true,
	'afterAjaxUpdate' => "$.fn.customFrontend.redrawRating",
	'itemsTagName' => 'div',
	'itemsCssClass' => "products-list products-list-small row $class",
	'template' => "<div class='filters-panel'>{sorter} {summary} <div class='pull-right hidden-xs'>{pager}</div></div> {items}",
	'sorterCssClass' => 'list-sorter pull-left sorter',
	'sorterHeader'=> t('front', 'Сортировать по:'),
    'sortableAttributes' => [
        'price' => t('front', 'Цене'),
        'created' => t('front', 'Дате')
    ],
	'pager'		=> 'app.widgets.productfilters.ProductPager',
	'viewMode'	=>	isset($viewMode) ? false : true,
]);?>