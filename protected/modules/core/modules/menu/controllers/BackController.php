<?php

/**
* BackController
*
* контроллер для создания и редактирования меню
*/
class BackController extends BController
{
	public $languageSelector = 'tree';

	public function actionIndex(){

		$model = $this->loadModel('Menu', false, false, true);
		$data = Menu::model()->roots()->findAll();
		$treeArray = [];

		if(request()->isAjaxRequest){
			$id = request()->getPost('id');
			if($id){
				$treeArray = Menu::model()->multilang()->findByPk($id);
			}
			Common::jsonSuccess(true, ['tree' => $this->renderPartial('tree', compact('treeArray'), true)]);
		}
		else {
			$this->pageTitle = 'Меню';
			$this->breadcrumbs = ["Продажи" => "#", 'Меню'];
			cs()->registerScriptFile($this->module->assetsUrl.'/js/menu.js');

			$this->render('index',  compact('data', 'treeArray', 'model'));
		}
	}

	// дерево
	public function actionGetInfo(){
		$id = request()->getPost('idMenu');
		Common::jsonSuccess(true, ['tree' => $this->renderPartial('tree', compact('treeArray'), true)]);
	}

	// добавляем в меню
	public function actionCreate(){
		$post = request()->getPost('Menu');
		$model = new Menu('create');
		$model->setAttributes($post);

		$options = ['itemOptions', 'htmlOptions', 'linkLabelWrapperHtmlOptions', 'submenuHtmlOptions', 'submenuOptions', 'linkOptions'];

		foreach($options as $item){
			$_i = array_filter($post[$item][0]);

			if(isset($post[$item]) && !empty($_i))
				$model->$item = CJSON::encode($post[$item]);
			else
				$model->$item = null;
		}

		$this->performAjaxValidation($model);

		$parent = $this->loadModel('Menu', $post['id_parent']);

		if($model->appendTo($parent))
			Common::jsonSuccess(true, ['idRootParent' => $post['idRootParent']]);

		Common::jsonSuccess(true, ['success' => false]);
	}

	// Создать новое меню
	public function actionCreateRootMenu(){
		$model = new Menu('create');
		$model->setAttribute('name', "Новое_меню(".date('H-i-s').")");
		$model->enabled = 1;

		if($model->saveNode())
			Common::jsonSuccess(true, ['new' => $this->renderPartial('new', ['menu' => $model], true)]);
		else
			Common::jsonSuccess(true, ['success' => false]);
	}

	// редактируем меню
	public function actionUpdateMenu(){

		$post  = request()->getPost('Menu');
		$model = $this->loadModel('Menu', $post['id'], false, true);
		
		$this->performAjaxValidation($model);

		if($post){
			$model->setAttributes($post);
			$options = ['itemOptions', 'htmlOptions', 'linkLabelWrapperHtmlOptions', 'submenuHtmlOptions', 'submenuOptions', 'linkOptions'];
			
			foreach($options as $item){
				if(isset($post[$item])){
					$_i = array_filter($post[$item][0]);
					if(!empty($_i))
						$model->$item = CJSON::encode($post[$item]);
				}else
					$model->$item = null;
			}
			
			if($model->saveNode()) 
				Common::jsonSuccess(true, ['idRootParent' => $post['idRootParent']]);	
		}
		
		Common::jsonSuccess(true, ['success' => false]);
	}

	// переставляем пункты в меню
	public function actionMove(){
		$idParent = request()->getPost('idParent');
		$idPrevNode = request()->getPost('idPrevNode');
		$nodeIds = request()->getPost('nodeIds');

		if($idPrevNode) $prevNode = $this->loadModel('Menu', $idPrevNode);

		$parent = $this->loadModel('Menu', $idParent);
		$root =  Menu::model()->findByPk($parent->root);

		foreach($nodeIds as $idNode){
			$model = $this->loadModel('Menu', $idNode);
			if(isset($prevNode)) $model->moveAfter($prevNode);
			else $model->moveAsFirst($parent);
			$prevNode = $model;
		}

		if($root){
    		Yii::app()->cache->delete(lang().".menu.".$root->slug);
    	}
	}

	// Подготовка данных для формы редактирования меню
	public function actionPrepareUpdate(){
		
		if($id = request()->getPost('id')){
			$model = $this->loadModel('Menu', $id, false, true);

			$result	= $model->fieldsValues;
			$result['idRootParent']	= request()->getPost('idRootParent');

			Common::jsonSuccess(true, $result);
		}
	}

	// Действие удаления меню
	public function actionDelete(){
		$model = $this->loadModel('Menu', request()->getPost('id'));

		if($model->deleteNode()){
			if($model->isRoot()){
				Common::jsonSuccess(true, ['rootId' => $model->id]);			
			}

			Common::jsonSuccess(true);
		}

		Common::jsonSuccess(true, ['success' => false]);
	}
}