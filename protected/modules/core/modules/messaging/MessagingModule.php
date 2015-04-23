<?php

class MessagingModule extends BaseModule
{
	public $moduleName = 'messaging'; //объязательно
	
	public $type = 'content';

	/**
	* @property string the path to the layout file to use for displaying Rights.
	*/
	public $layout = 'messaging.views.layouts.index';
	public $layoutTemplate = 'messaging.views.layouts.templates';

	/**
	* @property string the path to the application layout file.
	*/
	public $appLayout = '//layouts/main';
	
}
