<?php 


/**
* ActiveCaptchaValidator
*
* Валидатор для каптчи, когда требуется ajax валидация 
* в форме где есть каптча
*/
class ActiveCaptchaValidator extends CCaptchaValidator
{
	
	protected function validateAttribute($object, $attribute)
	{
		$code = Yii::app()->controller->createAction('captcha')->getVerifyCode();

		if ($code != $object->$attribute){
			$message = $this->message!==null? $this->message : 'Неправильный код проверки.';
			$this->addError($object, $attribute, $message);
		}

		if (!isset($_POST['ajax']))
			Yii::app()->controller->createAction('captcha')->getVerifyCode(true);
	}
}