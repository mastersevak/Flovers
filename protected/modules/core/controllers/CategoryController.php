<?php

/**
 * Category controller
 */
class CategoryController extends BController {

	public $view = '//main/category-form'; 
	public $hasManyRoots = false;
	public $rootID = 1;

	public function actionIndex()
	{
		$this->pageTitle = $this->title;
		$this->actionUpdate(true);
	}

	public function actionCreate()
	{
		$this->pageTitle = $this->title;
		$this->actionUpdate(true);
	}

	public function actionUpdate($new = false)
	{
		$this->pageTitle = $this->title;
		$modelName = $this->model;
		
		$this->layout = "//layouts/notabs2cols";
		
		if ($new === true)
			$model = new $modelName;
		else
		{
			$model = $modelName::model()
				->findByPk($_GET['id']);
		}

		if (!$model)
			throw new CHttpException(404, 'Категория не найдена.');

		$model->getMultilanguageValues();


		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST[$this->model];

			if ($model->validate())
			{
				if(!$this->hasManyRoots && $model->getIsNewRecord()){
					$parent = $modelName::model()->findByPk($this->rootID);
				 	$model->appendTo($parent);
				}
				else
					$model->saveNode();

				user()->setFlash('success', 'Изменения успешно сохранены');

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect( array('create') );
			}
		}

		$this->render($this->view, array('model'=>$model, 'rootID'=>$this->rootID));
	}

	/**
	 * Drag-n-drop nodes
	 */
	public function actionMoveNode()
	{
		$modelName = $this->model;
		
		$node = $modelName::model()->findByPk($_GET['id']);
		$target = $modelName::model()->findByPk($_GET['ref']);

		if(!$target){
				$node->moveAsRoot();
		}
		else
		{
			if((int) $_GET['position'] > 0){
				$pos = (int) $_GET['position'];
				$childs = $target->children()->findAll();
				if(isset($childs[$pos-1]) && $childs[$pos-1] instanceof $modelName && $childs[$pos-1]['id'] != $node->id)
					$node->moveAfter($childs[$pos-1]);
			}
			else
				$node->moveAsFirst($target);
		}
		

		$node->saveNode(false, array('full_path'));

	}

	/**
	 * Delete category
	 * @param array $id
	 */
	public function actionDelete($id = 0)
	{

		$modelName = $this->model;

		$model = $modelName::model()->findByPk($id);
		
		if($model->descendants()->count() > 0)
			throw new CHttpException(403, 'Ошибка удаления категории. Сперва нужно удалить все подкатегории.');

		//Delete if not root node
		if ($this->hasManyRoots || ($model && $model->id != 1) )
			$model->deleteNode();

		if (!Yii::app()->request->isAjaxRequest)
			$this->redirect( array('create') );
	}

}
