<?

class SActiveForm extends CActiveForm
{   
	public $enableClientValidation = true;

	public $modal = false;
	public $modalOptions = [];

	protected $defaultModalOptions = ['closeOnSuccess'=>true];
	protected $defaultHtmlOptions = ['autocomplete'=>'off'];

	public $afterModalClose = 'function(form, data){}'; //функция которая вызывается, после того как прошла валидация, и action вернул success  = true
	public $errorCallback = 'function(form, data){}'; //функция которая вызывается, после того как прошла валидация, но action вернул success  = false

	public function init(){

		$this->htmlOptions = CMap::mergeArray($this->defaultHtmlOptions, $this->htmlOptions);
		$this->modalOptions = CMap::mergeArray($this->defaultModalOptions, $this->modalOptions);

		if($this->enableAjaxValidation){

			if($this->modal){

				$callback = isset($this->clientOptions['afterValidate']) ? $this->clientOptions['afterValidate'] : 'function(form, data, hasError){return true;}';
				
				$this->clientOptions['afterValidate'] = "js:function(form, data, hasError){
					var result = ({$callback}) (form, data, hasError);

					if(result){
						if(!hasError){
							var action = form.data('ajax-action') != undefined ? 
									form.data('ajax-action') : form.prop('action');

							if(result){
								var sendData, settings = {};
								
								if(form.attr('enctype') == 'multipart/form-data'){
									sendData = new FormData(form.get(0));
									settings = Forms.uploadSettings();
								}
								else{
									sendData = form.serialize();
								}

								jPost(action, sendData, function(data){
					                if(data.success) {

					                   	({$this->afterModalClose})(form, data);

					                    if({$this->modalOptions['closeOnSuccess']}){
					                    	if($(form).closest('[role=\'domodal-dialog\']').length > 0)
												$(form).closest('[role=\'domodal-dialog\']').doModal('hide');
					                    }
										
										//enable submit
					                    Forms.enableFormSubmit(form);
					                    
					                    return false;
					                }
					                else {
										//enable submit
					                    Forms.enableFormSubmit(form);

					                    ({$this->errorCallback})(form, data);
					                }
					            }, 'json', settings);
							}
							
						}
						else{
							//enable submit
							Forms.enableFormSubmit(form);
						}	

						return false;
					}
					else
						return false;
					
				}";
			}
			elseif(!isset($this->clientOptions['afterValidate'])){
				$this->clientOptions['afterValidate'] = "js:function(form, data, hasError){
					if(!hasError){
						//тут произойдет form.submit() исходя из jquery.yiiactiveform.js
						return true;
					}
					else{
						//enable submit
						Forms.enableFormSubmit(form);
					}

					return false;
				}";
			}

			$this->clientOptions = CMap::mergeArray(array(
					'validateOnSubmit'=>true,
					'validateOnChange'=>true,
					'beforeValidate'=>'js:function(form, data, hasError){
						$(form).find(".errorMessage").hide();
						
						Forms.disableFormSubmit(form);
	        			
	        			return true;
	        		}'), $this->clientOptions);

		} //enableajaxvalidation


			
		
		parent::init(); //в данном случе только в конце
	}

	//поле показа даты
	public function dateField($model, $name, $readonly = true){
		return CHtml::textField((!$readonly ? get_class($model) : '').'_'.$name, 
			$model->$name ? Yii::app()->dateFormatter->format("dd MMM y, HH:mm:ss ", $model->timestamp($name)) : '', 
			array('readonly'=>$readonly));
	}

	//неактивное поле
	public function disableField($model, $name, $readonly = true){
		return CHtml::textField((!$readonly ? get_class($model) : '').'_'.$name, $model->$name, array('readonly'=>$readonly));
	}

	//поле для slug
	public function slugField($model, $name, $attributes = []){
		
		$attributes = CMap::mergeArray(['readonly'=>true], $attributes);

		echo CHtml::openTag('div', ['class'=>'slug clearfix']);
			echo CHtml::openTag('span', ['class'=>'slug-checkbox checkbox']);
				echo CHtml::checkBox('slug_set_'.actual($attributes['slugset-id'], ''));
				echo CHtml::label('В ручную', 'slug_set_'.actual($attributes['slugset-id'], ''));
			echo CHtml::closeTag('span');

			echo CHtml::openTag('span', ['class'=>'slug-text']);
				echo $this->textField($model, $name, $attributes);
			echo CHtml::closeTag('span');
		echo CHtml::closeTag('div');
	}

	//выборка даты
	public function datePicker($model, $name, $attributes = []){

		if($model->$name == 0)
			$model->$name = null;

		$attributes = CMap::mergeArray([
				'model'=>$model, 
				'attribute'=>$name,
				'pluginOptions'=>[
					'format'=>'dd/mm/yyyy', 
					'language'=>lang(),
					'weekStart' => '1',
				]
			], $attributes);

		echo CHtml::openTag('div', ['class'=>'input-append date-success success']);

		Yii::app()->controller->widget('yiiwheels.widgets.datepicker.WhDatePicker', $attributes);

		echo CHtml::openTag('span', ['class'=>'add-on']);
			echo CHtml::tag('span', ['class'=>'arrow'], '');
			echo CHtml::tag('i', ['class'=>'fa fa-calendar'], '');
		echo CHtml::closeTag('span');

		echo CHtml::closeTag('div');

	}

	//выборка диапазона
	public function dateRangePicker($model, $name, $attributes = []){

		if($model->$name == 0)
			$model->$name = null;

		$attributes = CMap::mergeArray([
				'model'=>$model, 
				'attribute'=>$name,
				'pluginOptions'=>UIHelpers::$dateRangeFilterOptions
			], $attributes);

		echo CHtml::openTag('div', ['class'=>'input-append success w200']);

		Yii::app()->controller->widget('yiiwheels.widgets.daterangepicker.WhDateRangePicker', $attributes);

		echo CHtml::openTag('span', ['class'=>'add-on']);
			echo CHtml::tag('span', ['class'=>'arrow'], '');
			echo CHtml::tag('i', ['class'=>'fa fa-calendar'], '');
		echo CHtml::closeTag('span');

		echo CHtml::closeTag('div');
	}

	//поле редактирования
	public function elrteEditor($model, $name, $attributes = []){
		$options = [
					'cssfiles' => ['css/elrte-inner.css'],
					'toolbar' => 'custom'
				];

		if(isset($attributes['options'])){
			$options = CMap::mergeArray($options, $attributes['options']);
			unset($attributes['options']);
		}

		if(isset($attributes['class'])) { 
			$options['cssClass'] = 'el-rte '.$attributes['class'];
			unset($attributes['class']);
		}

		Yii::app()->controller->widget('core.extensions.elrtef.elRTE', [
				'model'=>$model,
				'attribute'=>$name,
				'htmlOptions'=>$attributes,
				'options'=> $options
			]);
	}

	//IOS7 Switcher
	public function switcher($model, $attribute, $attributes = []){

		cs()->registerScriptFile(Yii::app()->controller->rootAssetsUrl.'/plugins/ios-switch/switchery.js');
		cs()->registerCssFile(Yii::app()->controller->rootAssetsUrl.'/plugins/ios-switch/switchery.css');

		$id = isset($attributes['id']) ? $attributes['id'] : get_class($model).'_'.$attribute;

		// cs()->registerScript('switchery_'.$id, "
		// 	if($.fn.switchers == undefined) $.fn.switchers = {};
		// 	$.fn.switchers['{$id}'] = new Switchery($('#{$id}').get(0), {color: '#0090d9'});");

		return $this->checkbox($model, $attribute, $attributes);
	}

	//select
	public function dropDownList($model, $name, $data, $htmlOptions = []){
		ob_start();
		$this->widget('core.components.TbSelect', [
		               'model' => $model,
		               'attribute' => $name,
		               'data' => $data,
		               'htmlOptions' => $htmlOptions
		            ]);
		
		$list = ob_get_contents();
		ob_end_clean();

		return $list;
	}

	public function textField($model, $attribute, $htmlOptions = []){
		
		$showLimits = isset($htmlOptions['data-limit']) && !isset($htmlOptions['hide-limit-text']);
		unset($htmlOptions['hide-limit-text']);

		$result = parent::textField($model, $attribute, $htmlOptions);

		if($showLimits)
			$result .= CHtml::tag('span', ['class' => 'limit'], 'Количество символов не должно превышать '.$htmlOptions['data-limit']);

		return $result;
	}

	public function textArea($model, $attribute, $htmlOptions = []){
		$showLimits = isset($htmlOptions['data-limit']) && !isset($htmlOptions['hide-limit-text']);
		unset($htmlOptions['hide-limit-text']);

		$result = parent::textArea($model, $attribute, $htmlOptions);

		if($showLimits)
			$result .= CHtml::tag('span', ['class' => 'limit'], 'Количество символов не должно превышать '.$htmlOptions['data-limit']);

		return $result;
	}

	//поле с маской
	public function maskedTextField($model, $name, $attributes = []){
		$params = ['model' => $model, 'attribute' => $name];

		if(isset($attributes['mask'])){
			$params['mask'] = $attributes['mask'];
			unset($attributes['mask']);
		} 

		if(isset($attributes['charMap'])){
			$params['charMap'] = $attributes['charMap'];
			unset($attributes['charMap']);
		} 

		if(isset($attributes['maskplaceholder'])){
			$params['placeholder'] = $attributes['maskplaceholder'];
			unset($attributes['maskplaceholder']);
		} 

		if(isset($attributes['completed'])){
			$params['completed'] = $attributes['completed'];
			unset($attributes['completed']);
		} 

		if(!empty($attributes)) $params['htmlOptions'] = $attributes;

		app()->controller->widget('CMaskedTextField', $params);
	}


	public function multilangTextField($model, $attribute, $htmlOptions = []){
		$defaultLanguage = param('defaultLanguage');
		$langs = param('languages');

		$result = '';
		
		foreach($langs as $key => $lang){
			$options = $htmlOptions;
			$options['data-language'] = $key;
			$options['class'] = isset($options['class']) ? $options['class'] . " multilang" : "multilang";
			$options['class'] .= " $key";

			$suffix = $key == $defaultLanguage ? '' : '_'.$key;
			
			if($key == $defaultLanguage) { 
				$options['value'] = $model->{$attribute."_".$key};
			}
			else {  
				//скрываем поля из других языков
				$options['class'] .= ' hidden';
				unset($options['data-slug-to']);
				unset($options['data-slugger']);
			}
			
			$options['hide-limit-text'] = true;

			$result .= $this->textField($model, $attribute.$suffix, $options);
		}

		if(isset($htmlOptions['data-limit']))
			$result .= CHtml::tag('span', ['class' => 'limit'], 'Количество символов не должно превышать '.$htmlOptions['data-limit']);
	
		echo $result;
	}


	public function multilangTextArea($model, $attribute, $htmlOptions = []){
		$defaultLanguage = param('defaultLanguage');
		$langs = param('languages');
		
		$result = '';
		
		foreach($langs as $key => $lang){
			$options = $htmlOptions;
			$options['data-language'] = $key;
			$options['class'] = isset($options['class']) ? $options['class'] . " multilang" : "multilang";
			$options['class'] .= " $key";

			$suffix = $key == $defaultLanguage ? '' : '_'.$key;
			
			if($key == $defaultLanguage) { 
				$options['value'] = $model->{$attribute."_".$key};
			}
			else {  
				//скрываем поля из других языков
				$options['class'] .= ' hidden';
				unset($options['data-slug-to']);
				unset($options['data-slugger']);
			}

			$options['hide-limit-text'] = true;

			$result .= $this->textArea($model, $attribute.$suffix, $options);
		}

		if(isset($htmlOptions['data-limit']))
			$result .= CHtml::tag('span', ['class' => 'limit'], 'Количество символов не должно превышать '.$htmlOptions['data-limit']);
	
		echo $result; 
	}	


	public function multilangElrteEditor($model, $attribute, $htmlOptions = []){
		$defaultLanguage = param('defaultLanguage');

		foreach(param('languages') as $key => $lang){
			$options = $htmlOptions;
			$options['options']['cssfiles'] = ['css/elrte-lang-'.$key.'.css'];
			$options['class'] = isset($options['class']) ? $options['class'] . " multilang" : "multilang";
			$options['class'] .= " $key";

			$suffix = $key == $defaultLanguage ? '' : '_'.$key;

			if($key == $defaultLanguage) { 
				$options['value'] = $model->{$attribute."_".$key};
			}
			else {
				//скрываем поля из других языков
				$options['class'] .= ' hidden';
			}
			
			echo $this->elrteEditor($model, $attribute.$suffix, $options);
		}
	}
}

?>
