<?php
Yii::import('app.controllers.FrontController');
class SiteController extends FrontController {

	public $model;
	public $bannerInfo = [];
	public $categoryID;//для баннеров
	public $tabs = [];
	public $category = ProductCategory::ROOT;

	public function behaviors(){
		return CMap::mergeArray(parent::behaviors(), [
			'comment.widgets.behaviors.CommentActions'
		]);
	}

	public function init(){
		parent::init();
		cs()->registerScriptFile($this->assetsUrl.'/js/front-profile.js');

		cs()->registerPackage('jalerts'); //alerts
		cs()->registerCoreScript('cookie');
	}

	public function allowedActions(){
		return "install, error, backenderror";
	}

	/**
	 * Главная страница
	 */
	public function actionIndex(){
		$this->layout = '//layouts/home';
		$this->render('blank');
	}

	/**
	 * Список продуктов
	 */
	public function actionList($category = false){
		$model = new Product('search');
		$this->model = $model;
		$itemView = '_listThumbMedium';

		if($category){
			$category = ProductCategory::model()->find('slug = :slug', [':slug' => $category]);
			if(!$category) throw exception(404);

			$this->category = $category->id;
		}

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			$get = request()->getParam('Product');
			if($get) $model->attributes = $get;
			$dataProvider = $model->frontSearch(false, $category);
			$this->renderPartial('products/_list', compact('dataProvider', 'itemView'));
		}
		else{
			cs()->registerScriptFile($this->assetsUrl.'/js/list.js');

			$this->breadcrumbs = [ t('front', "Продукты") => ["/site/list"]];
			if($category){
				$this->breadcrumbs[] = $category->name;
				$this->categoryID = $category->id;
			}

			$this->layout = 'list';
			$dataProvider = $model->frontSearch(false, $category);
			$this->render('products/list', compact('model', 'dataProvider', 'itemView'));
		}
	}

	/**
	 * Список брендов
	 */
	public function actionBrands(){
		$model = new ProductBrand('search');

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			$this->renderPartial('brands/_list', compact('model'));
		}
		else{
			$this->render('brands/list', compact('model'));
		}
	}

	/**
	 * Список мастеров
	 */
	public function actionPersons(){

		$model = new Person('search');

		if(request()->isAjaxRequest && request()->getParam('ajax'))
			$this->renderPartial('persons/_list', compact('model'));

		else $this->render('persons/list', compact('model'));
	}

	/**
	 * Корзина
	 */
	public function actionCart(){
		cs()->registerCoreScript('maskedinput');

		$this->breadcrumbs = [t('front', "Корзина")];

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			$this->renderPartial('cart');
		}else{
			$this->render('cart');
		}
	}

	//items ----------------------------------------
	// открытый продукт
	public function actionItem($id){
		$model = $this->loadModel('Product', $id, ['person']);

		// comments
		$comments = Comment::model()->provider(Comment::TYPE_PRODUCT, $id);

		$this->breadcrumbs = [ t('front', "Продукты") => "/site/list", $model->title];
		$this->render('products/item', compact('model', 'comments'));
	}

	public function actionBrand($id){
		$model = $this->loadModel('ProductBrand', $id, ['products', 'photo']);

		$product = new Product('search');
		$criteria = new CDbCriteria;
		$criteria->compare('id_brand', $id);
		$dataProvider = $product->frontSearch($criteria);

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			if(request()->getParam('getItems')){
				$_GET = $_POST;
				Common::jsonSuccess(true, ['items' => $this->renderPartial('products/_listAjax', compact('dataProvider'), true)]);
			}
			else{
				$this->renderPartial('products/_list', compact('dataProvider'));
			}
		}
		else{
			cs()->registerScriptFile($this->assetsUrl.'/js/list.js');
			$this->breadcrumbs = [ t('front', "Бренды") => ["/site/brands"], $model->name];
			$this->render('brands/item', compact('model', 'dataProvider'));
		}
	}

	/**
	 * Открытая страница указанного мастера
	 * @param  int 		$id айди мастера
	 */
	public function actionPerson($id){
		$this->layout = '//layouts/home';
		$model = $this->loadModel('Person', $id);
		cs()->registerScriptFile($this->assetsUrl.'/js/person.js');


		$product = new Product('search');
		$criteria = new CDbCriteria;
		$criteria->compare('id_owner', $id);
		$dataProvider = $product->frontSearch($criteria);

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			if(request()->getParam('getItems')){
				$_GET = $_POST;
				Common::jsonSuccess(true, ['items' => $this->renderPartial('products/_listAjax', compact('dataProvider'), true)]);
			}
			else{
				$this->renderPartial('products/_list', compact('dataProvider'));
			}
		}
		else {
			cs()->registerScriptFile($this->assetsUrl.'/js/list.js');
			$this->breadcrumbs = [ t('front', "Мастера"), Person::listData()[$model->id]];
			$this->render('persons/item', compact('model', 'dataProvider'));
		}
	}

	public function actionProfile(){
		if(user()->isGuest) $this->redirect('/login');

		cs()->registerScriptFile($this->assetsUrl .'/js/person.js');

		$this->pageTitle = t('front','Профиль');

		$model = $this->loadModel('Person', user()->id, ['profile']);

		$this->performAjaxValidation($model);

		if(request()->getPost('Person')){

			$model->setAttributes($_POST);

			if($model->save()){

				//сохранение настроек профиля
				if($model->id == user()->id)
					user()->UpdateInfo($model); //обновить данные текущего пользователя

				$this->refresh();
			}
		}

		$changePasswordModel = $model;
		$changePasswordModel->scenario = 'resetPassword';
		$changePasswordModel->password = '';

		$this->tabs = ['about' => t('front', 'Общая информация'), 'products' => t('front', 'Продукты'), 'message' => t('front', 'Сообщения'), 'orders' => t('front', 'Заказы')];

		if(request()->isAjaxRequest && request()->getParam('ajax')){
			$model 	  = new product;
			$itemView = '_listThumbBig';
			$viewMode = true;
			$this->renderPartial('products/_list', compact('model', 'itemView', 'viewMode'));
		}
		else
			$this->render('/auth/profile', compact('model', 'changePasswordModel'));
	}

	public function actionProduct(){

		$this->render('product');
	}

	public function actionProductCreate($id = false){

		if(user()->isGuest) $this->redirect('/login');

		$this->pageTitle = t('front','Создание продукта');

		if(isset($_POST['Product']) && $id)
		{
			$model = $this->loadModel('Product', $id);
			$model->status = Product::STATUS_INACTIVE;
			$model->id_owner = user()->id;

			$this->performAjaxValidation($model);

			$model->attributes = $_POST['Product'];

			if($model->validate()){

				if(!empty($model->withRelatedObjects)){
					$result = $model->withRelated->save(false, $model->withRelatedObjects);
				}
				else {

					$result = $model->save(false);
				}

				$this->redirect('/site/productcreate');
			}
		}
		else{
			$model = new Product('create');
			$model->status = Product::STATUS_TEMP;
			$model->save(false);
		}

		$files = $model->photos;
		$params = 'product';

		$this->render('product_create', compact('model','files', 'params'));
	}

	public function actionEditProduct($id){

		if(user()->isGuest) $this->redirect('/login');

		$this->pageTitle = t('front','Редактирование продукта');

		$model = $this->loadModel('Product', $id);

		if(isset($_POST['Product']))
		{
			$this->performAjaxValidation($model);

			$model->attributes = $_POST['Product'];

			if($model->validate()){

				if(!empty($model->withRelatedObjects)){
					$result = $model->withRelated->save(false, $model->withRelatedObjects);
				}
				else
					$result = $model->save(false);

				$this->redirect('/site/profile');
			}
		}
		elseif($model->id_owner == user()->id){

			$this->render('updateProduct', compact('model'));
		}
	}

	public function actionDeleteProduct($id){
		$model = $this->loadModel('Product', $id);
		if($model->id_owner == user()->id)
			if($model->delete()){
				Common::jsonSuccess(true);
		}

		Common::jsonError("Ошибка при удалении");
	}

	/**
	 * Новость
	 */
	public function actionNews($slug = false){
		// Список Новостей
		if(!$slug){
			$this->breadcrumbs = [ t('front', "Новости") => ["/site/news"]];

			$model = new News('search');
			if(request()->isAjaxRequest && request()->getParam('ajax')){
				$this->renderPartial('news/_list', compact('model'));
			}
			else{
				$this->render('news/list', compact('model'));
			}
		}
		//Новость в открытом виде
		else{
			$model = News::model()->active()->find('slug=:slug', array(':slug'=>$slug));

			//увеличиваем посещение
			$model->saveCounters(['visits'=>1]);

			$this->breadcrumbs = [ t('front', "Новости") => ["/site/news"], $model->title];
			$this->render('news/item', compact('model'));
		}
	}

	public function actionError(){

		// $this->layout = '//layouts/error';
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else{
				/*if($error['code'] == 404)
					$this->render('404');
				else
					$this->render('error', $error);*/

				echo "<h4>Ошибка ".$error['code']."</h4>";
				echo "<p>".$error['message']."</p>";
			}

		}
	}

	public function actionBackendError(){
		app()->theme = 'backend';
		$this->layout = '/layouts/error';

        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('modules.admin.views.errors.'.$error['code'], $error);
        }
	}

	/**
	 * СТАТИЧЕСКИЕ СТРАНИЦЫ
	 */
	public function actionPage($route){

		$page = Page::model()->find('route =:route', array(':route'=>$route));

		if(!$page) exception(404);

		$this->pageTitle = actual($page->meta_title, $this->pageTitle);
		$this->pageKeywords = actual($page->meta_keywords, $this->pageKeywords);
		$this->pageDesc = actual($page->meta_description, $this->pageDesc);

		$this->render('page', compact('page'));
	}

	/**
	 * Функция выдает все сообщение связонные с idChat
	 */
	public function actionGetMessages(){
		$idChat = request()->getParam('idChat');
		$models = ChatMessage::model()->findAll('id_chat = :id_chat',[':id_chat'=> $idChat]);
		$idUser = request()->getParam('idUser');

		$result = false;
		if($models)
			$result = $this->renderPartial('/auth/_messageRight', compact('models', 'idUser', 'idChat'), true);

		if($result)
			Common::jsonSuccess(true, ['messages' => $result]);
	}

	/**
	 * Функция сохраняет сообщения
	 */
	public function actionSendMessage(){

		$message = request()->getParam('message');
		$idUsers = request()->getParam('idUsers');
		$idChat = request()->getParam('idChat');

		$model = new ChatMessage;
		$model->id_user = user()->id;
		$model->id_chat = $idChat;
		$model->message = $message;

		if($model->save()){

			Common::jsonSuccess(true,['selfmessage' => $this->renderPartial('/auth/_selfMessage', compact('model'), true)]);
		}
	}

	//Функция проверяет есть ли переговоры с этим пользователем
	//и если ест то добавляет в переговоры, а если нет то добавляет
	public function actionSaveNewMessage(){

		$id = request()->getParam('id');
		$message = request()->getParam('message');

		if(in_array($id , array_keys(ChatUser::getAllUsersInCHat($id)))){
			$idChat = ChatUser::getAllUsersInCHat($id)[$id];
		}
		else{
			$idChat = ChatUser::getMaxChatId();
			$chatUser = new ChatUser;
			$chatUser->id_chat = $idChat;
			$chatUser->id_user = user()->id;
			$chatUser->save();

			$chatUser = new ChatUser;
			$chatUser->id_chat = $idChat;
			$chatUser->id_user = $id;
			$chatUser->save();
		}

		$model = new ChatMessage;
		$model->id_user = user()->id;
		$model->id_chat = $idChat;
		$model->message = $message;
		if($model->save())
			Common::jsonSuccess(true);


	}


	/**
	 * Функция для поиска пользователей сообщения
	 */
	public function actionSearchMessageUser(){
		$text = request()->getParam('text');
		$list = false;
		if($text)
			$list = array_keys(preg_grep('#.*'.$text.'.*#i', Person::listData()));

		$data = ChatUser::getAllUsersInCHat($list);
		Common::jsonSuccess(true, ['left' =>  $this->renderPartial('/auth/_messageLeft', compact('data'), true)]);

	}

	// Функция для сохранение рейтингов
	public function actionSaveRating(){

		$rating = request()->getParam('score');
		$id_product = request()->getParam('id');

		if($rating && $id_product){
			$model = new ProductRating;
			$model->rating = $rating;
			$model->id_product = $id_product;
			$model->id_creator = user()->id;
			if(!$model->save())
				Common::jsonSuccess(true,['success' => false, 'errors' => $model->getErrors()]);
		}
	}

	// подготовка к обновлению модального окна procucta
	public function actionPrepareUpdate(){
		$id = app()->request->getPost('id');
		$model = app()->request->getPost('model');

		if($id && $model){
			$model  = $this->loadModel($model, $id);
			$result = $model->attributes;

			if($model->size){
				$result['width'] = CJSON::decode($model->size)['width'];
				$result['height'] = CJSON::decode($model->size)['height'];
			}

			Common::jsonSuccess(true, ['success' => true] + $result);
		}
	}
}
