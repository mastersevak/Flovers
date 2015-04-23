<?
$model = new Product('search');
$this->renderPartial('/site/updateProduct', compact('model'));?>

<?
$criteria = new CDbCriteria;
$criteria->compare('id_owner', $id);
$this->widget('SGridView', [
		'id' => 'grid',
		'dataProvider'=>$model->search($criteria),
		'filter'=>$model,
		'showCheckBoxColumn' => false,
		'showHiddenColumns' =>false,
		'template' => '{items}',
		'ajaxUrl' => $this->createUrl('profile'),
		'columns'=>[
			[
				'class'	 => 'SDateColumn',
				'name'	 => 'created',
				'rangeFilter' => false,
				'headerHtmlOptions' => ['width' => 90, 'align'=>'center'],
				'value'	 => function($data){
					return date('d/m/Y', strtotime($data->created));
				}
			],
			[
				'name'	=> 'title',
				'headerHtmlOptions' => ['align'=>'center'],
			],
			[
				'name'	=> 'price',
				'headerHtmlOptions' => ['width'=>100, 'align'=>'center'],
				'value'	=> function($data){
					return $data->price ? $data->price." ".Product::CURRENCY_ARM : '';
				}
			],
			[
				'class'  => 'SButtonColumn',
				'headerHtmlOptions' => ['width' => 60],
				'buttons' 	  => [
					'delete' => ['url' => 'url("/site/deleteproduct", ["id"=>$data->id])'],
					// 'update' => ['url' => 'url("/site/editproduct", ["id"=>$data->id])'],
					'update' => [
						'options' => [
							'class'			=>	'product-front-update',
							'data-url'		=>	url('/site/prepareupdate'),
							'data-target'	=>	'#product-update-modal',
							'data-model'	=>	'Product',
							'data-title'	=>	'РЕДАКТИРОВАНИЕ ПРОДУКТА',
							'data-action'	=>	url('/site/editproduct'),
							'data-id'		=>	'$data->id',
							'onclick'		=>	'$.fn.frontProfile("openModal", $(this));return false;',
						],
					],
				],
			]
		],
	]);
?>