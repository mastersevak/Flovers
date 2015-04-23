<?php 


/**
 * IndexAction
 * 
 * Creates a new model
 */
 class IndexAction extends CAction
 {
 	public $title = 'Управление пользователями';
 	public $breadcrumbs = ['Пользователи'];
 	public $beforeRender;
 	public $view = 'index';
 	public $ajaxView;
 	public $model = 'User';

 	public $tabs;

 	public $multilang = false;

 	// метод который показывает список пользователей
	public function run(){
		$model = $this->model;
		$model = new $model('search');
		
		$criteria = new SDbCriteria;

		if(($type = Yii::app()->getRequest()->getParam('type', 'all')) || $type == ''){
			
			$blocked = Yii::app()->db->createCommand()
									->select('id_user')
									->from('{{user_block}}')
									->queryColumn();

			switch($type){ //показать только заблокированных пользователей
				case 'blocked':
					if(!$blocked) $blocked = '-1';
					$criteria->compare('t.id', $blocked);
					break;

				default:
					$criteria->compare('t.id !', $blocked);
					break;
			}	
			
		}

		$provider = $model->search($criteria);

		if(Yii::app()->request->isAjaxRequest && Yii::app()->request->getParam('ajax')){
        	$this->controller->renderPartial($this->view, compact('provider', 'type'));
		}
        else{
        	$this->controller->layout = "//layouts/tabs";
        	$this->controller->pageTitle = $this->title;
			
			$this->controller->breadcrumbs = $this->breadcrumbs;
			
			$this->controller->tabs = [
				''=>t('admin', 'Все'),
				'blocked'=>t('admin', 'Заблокированные')
				];

       		$this->controller->render($this->view, compact('provider', 'type'));
        }
	}
 }