<?php

class CheckAccessFilter extends CFilter
{
	public $minLevel;

    protected function preFilter($filterChain)
    {
        if (!user()->isGuest && $this->minLevel < user()->getState('role') ){
	        throw new CHttpException(403, 'You have no permission to view this content');
	        return false;
	    }

        return true; // false — для случая, когда действие не должно быть выполнено
    }
 
    protected function postFilter($filterChain) 
    {
    }
}