<div class="row">
	<div class="col-md-9">
		<div class="grid simple">
	    	<div class="buttons mb20">
	    		<? $this->widget('UIButtons', ['buttons'=>['create', 'deleteSelected', 'clearFilters'], 'size' => 'small']) ?>
	    	</div>

			<?$this->widget('SGridView', array(
				'id' => 'settings-grid',
				'dataProvider'=>$model->search(),
				'filter'=>$model,
				'sortable'=>true,
				'flexible'=>true,
				'columns'=>array(
					array(
						'name'  => 'title',
						'type' => 'html',
						'value' => function($data){return CHtml::link($data->title, $data->backUrl);},
					),
					array(
						'name'  => 'code',
						'type' => 'html',
						'value' => function($data){return CHtml::link($data->code, $data->backUrl);},
					),
					array(
						'name'  => 'value',
						'type' => 'html',
						'value' => function($data){return CHtml::link($data->value, $data->backUrl);},
					)
				),
			));?>
		</div>
	</div>

	<div class="col-md-3">
		<div class="grid simple vertical green">
        	<div class="grid-title">
        		<h4>Категории</h4>
        	</div>

		    <div class="grid-body right-menu">
				<?=Settings::getCategories(true)?>
		    </div>

		</div>
	</div>
</div>

