<?php 


/**
* HasManyBehavior
*/
class HasManyBehavior extends CBehavior
{

	public $relations;

	public function prepareForSave($model){

		foreach($this->relations as $relation => $_model){

			if(isset($_POST[$_model])){
				$elements = [];

				foreach($_POST[$_model] as $row){
					$element = new $_model;
					$element->attributes = $row;

					if($element->validate()){
						$elements[] = $element;
					}
				}

				if(!empty($elements)){
					$model->withRelatedObjects[] = $relation;
					$model->{$relation} = $elements;
				}
			}

		}


		
	}
}