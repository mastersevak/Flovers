<?php

class UnlockFilter extends CFilter
{
	public $minLevel;

    protected function preFilter($filterChain)
    {
        if (!user()->isGuest && user()->getState('userLocked') ){
	        Yii::app()->controller->redirect( array('/user/back/unlock') );
	        return false;
	    }

        return true; // false — для случая, когда действие не должно быть выполнено
    }
 
    protected function postFilter($filterChain) 
    {
        // код, выполняемый после выполнения действия
    }
}