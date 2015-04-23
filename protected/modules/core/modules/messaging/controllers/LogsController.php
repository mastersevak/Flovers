<?php

/**
* NotifyController - контроллер для сохранения отправленных sms, email-ов, log-ов 
*/
// Yii::import('core.components.sms.models.Sms'); //@todo: change to Yii::import
// Yii::import('core.components.sendmail.models.Mail');

class LogsController extends BController {
	
	public $title = 'Логи';

	/**
	* Initializes the controller.
	*/
	public function init(){
		parent::init();

		$this->layout = $this->module->layout;


	}

	public function actionIndex(){
		$this->redirect(['info']);
	}

	public function actionInfo(){
		$model = new Log('search');
		$this->pageTitle = $this->title;

		if(request()->isAjaxRequest){
			$post = request()->getParam('Log');
			$model->attributes = $post;
		}
		
		$this->render('log', ['model' => $model, 'level' => Log::LEVEL_INFO]);
	}

	public function actionWarning(){
		$model = new Log('search');
		$this->pageTitle = $this->title;

		if(request()->isAjaxRequest){
			$post = request()->getParam('Log');
			$model->attributes = $post;
		}
		
		$this->render('log', ['model' => $model, 'level' => Log::LEVEL_WARNING]);
	}

	public function actionError(){
		$model = new Log('search');
		$this->pageTitle = $this->title;

		if(request()->isAjaxRequest){
			$post = request()->getParam('Log');
			$model->attributes = $post;
		}

		$this->render('log', ['model' => $model, 'level' => Log::LEVEL_ERROR]);
	}

	public function actionSms(){
		$model = new Sms('search');
		$this->pageTitle = $this->title;

		if(request()->isAjaxRequest){
			$post = request()->getParam('Sms');
			$model->attributes = $post;
		}
		
		$this->render('sms', compact('model'));
	}

	public function actionEmail(){
		$model = new Mail('search');
		$this->pageTitle = $this->title;

		if(request()->isAjaxRequest){
			$post = request()->getParam('Mail');
			$model->attributes = $post;
		}

		cs()->registerScriptFile($this->module->assetsUrl.'/js/logs.js');
		
		$this->render('email', compact('model'));
	}

	public function actionResend(){
		if(request()->isAjaxRequest){
			$id = request()->getParam('id');
			$model = request()->getParam('model');

			$model == 'Mail' ? app()->mail->sendId($id, true) : app()->sms->sendId($id); 

    		echo CJSON::encode(['success' => true]);
		}
	}
}