<?php

/**
* SCaptchaAction
*/
class SCaptchaAction extends CCaptchaAction
{
	public $backColor = 0xDDDDDD;
    public $maxLength = 4;
    public $minLength = 4;
    public $height = 38;
    public $foreColor = 0x000000;

    public $skipOnAjax = true;
	
	/**
	 * Валидатор для каптчи, когда не требуется ajax валидация 
	 * в форме где есть каптча
	 */
	public function validate($input, $caseSensitive){
		if($this->skipOnAjax && request()->isAjaxRequest) return true;
		//переопределяем функцию валидации, 
		//чтобы после каждой пройденной валидации картинка менялась
		if(parent::validate($input, $caseSensitive)){
			
			$this->getVerifyCode(true);

			return true;
		}
		return false;

	}
}