<?

class SPopupForm extends CActiveForm
{   
	public $enableClientValidation = true;
	public $enableAjaxValidation = true;

	public $clientOptions;
	public $id = 'popup-form';

	public function init(){
		parent::init();

		$url = CJavaScript::encode(app()->controller->assetsUrl.'/images/loaders/loader11.gif');

		$afterValidate =<<<script
js:function(form, data, hasError){
	if(!hasError){
		$.post('{$this->action}', 
			{form_data: $("#message-form").serialize(), ajax_submit:1}, 
			function(success){
				//window parent - так как нужен доступ к родительским элементам из iframe
				window.parent.$('#popup_close').trigger('click');

				//$('#popup_close').trigger('click'); - данный случай нужен в случае без iframe

			});

		$("#popup_container").remove();
	}
	return false;
	
}
script;

		$this->clientOptions = array(
			'validateOnSubmit'=>true,
			'validateOnChange'=>true,
			'afterValidate'=>$afterValidate

		);

	}
}

?>
