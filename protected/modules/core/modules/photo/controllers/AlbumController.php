<?php


class AlbumController extends BController
{
	
	public $model = 'Photoalbum'; //for loadModel function
	
	public $views = array(
			'index'  => '/album/index',
			'create' => '/album/create',
			'update' => '/album/update'
		);


	public function filters(){
		return CMap::mergeArray(parent::filters(), array(
            'postOnly + deleteThumbnail',
        ));
	}

	//метод который показывает список фотоальбомов
	public function actionIndex(){
		
		$this->pageTitle = t('admin', 'Управление фотоальбомами');
		$this->pageDesc = t('admin', 'Можно ввести оператор сравнения (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) в начале каждого из поиска, чтобы указать, как сравнение должно быть сделано.');

		$criteria = new SDbCriteria;
		$_model = $this->model;
		
		$model = new $_model('search');

		//для списков всегда вставлять этот кусок кода
		if (intval(app()->request->getParam('clearFilters'))==1) {
            SButtonColumn::clearFilters($this, $model);
        }

		$provider = $model->search($criteria);

		$this->render($this->views['index'], compact('provider', 'model'));
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' news.
	 */
	public function actionCreate($title = false){

		$this->layout = '//layouts/tabs';

		if(!$title){
			$this->pageTitle = t('admin', 'Создание фотоальбома');
			$this->pageDesc  = t('admin', 'Заполните поля для создания фотоальбома');
		}
		
		if(!$this->tabs)
			$this->tabs = array(
				'main'=>t('admin', 'Общая информация')
				);
		
		$_model = $this->model;
		$model = new $_model('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$_model]))
		{
			$model->attributes=$_POST[$_model];
			

			if($model->save()){

				//объязательно
				if(isset($_GET['close']) && $_GET['close'] == 'true')
					$this->redirect(user()->gridIndex);

				$this->redirect(array('update', 'id'=>$model->id));
			}
		}

		$this->render($this->views['create'], compact('model'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id, $params = 'photoalbumPhoto', $title = false){

		$this->layout = '//layouts/tabs';

		if(!$title){
			$this->pageTitle = t('article', 'Редактирование фотоальбома');
			$this->pageDesc  = t('admin', 'Заполните поля для редактирования фотоальбома');
		}
		
		if(!$this->tabs)
			$this->tabs =array(
				'main'=>t('admin', 'Общая информация'),
				'photos'=>t('admin', 'Фотографии'),
				);

		$_model = $this->model;
		$model = $this->loadModel($this->model, $id);

		if($_model::$hasComments) $this->tabs['comments'] = t('admin', 'Комментарии');
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$_model]))
		{

			$model->attributes = $_POST[$_model];
			
			if($model->save()){
				//TODO: в других проектах удалить
				//только для проекта yerevanresto, установить главную фотку в кеш
				if($params == 'photoalbumPhoto' && $leisures = $model->leisures){
					$leisures[0]->cacheMainPhoto();
				}

				//объязательно
				if(isset($_GET['close']) && $_GET['close'] == 'true') 
					$this->redirect(user()->gridIndex);
				else
					$this->refresh();
			}
				
		}

		//переменные для фоток
		$files = $model->photos;

		$this->render($this->views['update'], compact('model', 'files', 'params'));
	}

}
