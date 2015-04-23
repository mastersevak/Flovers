<?php 


/**
* AjaxController in backend
*/
class AjaxController extends BController
{
	// public function filters(){
	// 	return CMap::mergeArray(parent::filters(), ['postonly']);
	// }
	
	//получить CSRF
	public function actionCsrf(){
		echo CJSON::encode(array(
			Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken()
			));
	}

	public function actionAutocomplete(){

		if($keyword=trim(Yii::app()->request->getParam('term')))
		{
			$model = Yii::app()->request->getParam('model');
			try{
				$suggest = $model::autocomplete($keyword);
				echo CJSON::encode($suggest);
			}
			catch(Exception $e){
				//@todo: show error
			}
			
		}
	}

	/**
	 * Получаем историю изменений для указанного поля, указанной модели
	 */
	public function actionFieldHistory(){
		$model = request()->getParam('model');
		$id = request()->getParam('id');
		$field = request()->getParam('field');
		$count = request()->getParam('count', 1);
		$dateFormat = request()->getParam('dateFormat');

		$date = false;
		$user = false;

		$result = [];

		if($model && $id && $field){
			$model = $this->loadModel($model, $id);

			$behaviors = $model->behaviors();
			$behavior = isset($behaviors['catchChanges']) ? $behaviors['catchChanges'] : false;

			//если catchChanges behavior не указан для модели
			if(!$behavior) Common::jsonError('Behavior CatchChanges не указан для данной модели', true);

			//если параметр не указан в атрибутах catchChanges
			if(!isset($behavior['attributes']) || 
				(!isset($behavior['attributes'][$field]) && !in_array($field, $behavior['attributes'])) )
					Common::jsonError("Поле {$field} отсутствует в списке отслеживаемых параметров CatchChanges behavior", true);
			
			$dateField = $field.'_date';
			if(array_key_exists($field, $behavior['attributes']) && isset($behavior['attributes'][$field]['dateField']))
				$dateField = $behavior['attributes'][$field]['dateField'];

			$userField = $field.'_user';
			if(array_key_exists($field, $behavior['attributes']) && isset($behavior['attributes'][$field]['userField']))
				$userField = $behavior['attributes'][$field]['userField'];

			$historyTable = isset($behavior['historyTable']) ? $behavior['historyTable'] : $model->tableName(). "_events_history";
			
			$command = Yii::app()->db->createCommand()
								->select("date, id_user, old_value, new_value")
								->from($historyTable)->where("id_object = :id and field_name = :field", 
									[":id" => $id, ":field" => $field])->order("date desc");

			if($count == 1){ //только последнее изменение
				//смотрим в текущей таблице, если нашли используем данные оттуда
				//если же не нашли смотрим в таблице истории

				if($model->hasAttribute($dateField) && $model->hasAttribute($userField)){
					$date = $dateFormat ? date($dateFormat, strtotime($model->$dateField)) : date('d-m-Y H:i', strtotime($model->$dateField));
					$user = ['id' => $model->$userField, 'name' => isset(Employee::listData()[$model->$userField]) ? Employee::listData()[$model->$userField] :''];
				}
				else{
					if(!Yii::app()->db->getSchema()->getTable($historyTable))
						Common::jsonError('Таблица истории не существует', true);

					$row = $command->limit(1)->queryRow();

					if($row) { 
						$date = $dateFormat ? date($dateFormat, strtotime($row['date'])) : date('d-m-Y H:i', strtotime($row['date']));
						$user = ['id' => $row['id_user'], 'name' => Employee::listData()[$row['id_user']]];
						$old = $row['old_value'];
						$new = $row['new_value'];
					}
				}	

				$result = compact('user', 'date');
			}
			else{ //последние несколько изменений
				if(!Yii::app()->db->getSchema()->getTable($historyTable))
					Common::jsonError('Таблица истории не существует', true);

				$rows = $command->limit($count)->queryAll();

				foreach($rows as $row){
					$result[] = [
						'date' => $dateFormat ? date($dateFormat, strtotime($row['date'])) : date('d-m-Y H:i', strtotime($row['date'])),
						'user' => ['id' => $row['id_user'], 'name' => Employee::listData()[$row['id_user']]],
						'old' =>  $row['old_value'],
						'new' =>  $row['new_value']
					];
				}
			}

			Common::jsonSuccess(true, compact('result'));
		}
		else{
			Common::jsonError('Указаны не все параметры для запроса');
		}
	}

	/**
	 * Generate and return url for saved filters
	 * сейчас уже не используется
	 */
	public function actionFiltersUrl(){
		$modelName = request()->getParam('model');
		if($modelName){
			$model = new $modelName('search');
			Common::jsonSuccess(true, ['url' => $model->generateUrl(request()->getParam('_url'))]);
		}

		Common::jsonError('Не верный запрос');
		
	}

	/**
	 * Возвращает список брендов для селектов
	 */
	public function actionBrands(){
		if($searchStr = request()->getPost('searchStr')){
			
			$criteria = new CDbCriteria;
			$criteria->compare('name', $searchStr, true);
			$criteria->limit = 10;

			$options = Brand::model()->queryAll($criteria, [], ['id', 'name'], true);

			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Возвращает список контрагентов для селектов
	 */
	public function actionSuppliers(){
		if($searchStr = request()->getPost('searchStr')){
			
			$criteria = new CDbCriteria;
			$criteria->compare('name', $searchStr, true);
			$criteria->limit = 10;

			$options = Supplier::model()->queryAll($criteria, [], ['id', 'name'], true);

			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Возвращает список контрагентов для селектов
	 */
	public function actionLegalEntities(){
		if($searchStr = request()->getPost('searchStr')){

			$criteria = new CDbCriteria;
			$criteria->compare('full_name', $searchStr, true);
			$criteria->limit = 10;

			$options = UserLegalEntity_Old::model()->queryAll($criteria, [], ['id', 'full_name'], true);
			
			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Возвращает список metro для селектов
	 */
	public function actionMetros(){
		if($searchStr = request()->getPost('searchStr')){
			
			$criteria = new CDbCriteria;
			$criteria->compare('name', $searchStr, true);
			$criteria->limit = 10;

			$options = Metro::model()->queryAll($criteria, [], ['id', 'name'], true);

			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Возвращает список складов для селектов
	 */
	public function actionStorehouses(){
		if($searchStr = request()->getPost('searchStr')){

			$criteria = new CDbCriteria;
			$criteria->compare('name', $searchStr, true);
			$criteria->limit = 10;

			$options = Storehouse::model()->queryAll($criteria, [], ['id', 'name'], true);
			
			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Возвращает список категорий продуктов для селектов
	 */
	public function actionCategories(){
		if($searchStr = request()->getPost('searchStr')){

			$criteria = new CDbCriteria;
			$criteria->compare('name', $searchStr, true);
			$criteria->limit = 10;

			$options = ProductCategory::model()->queryAll($criteria, [], ['id', 'name'], true);
			
			Common::jsonSuccess(true, ['items' => $options]);	
		}
	}

	/**
	 * Изменение полей contenteditable
	 */
	public function actionContentEditable(){

		$model = request()->getParam('model'); //Модель
		$attribute = request()->getParam('attribute'); //поле которое меняется
		$searchField = request()->getParam('searchfield', 'id'); //поле для поиска
		$searchValue = request()->getParam('searchvalue'); //значение для поиска
		$value = request()->getParam('value'); //значение на которое меняется
		$scenario = request()->getParam('scenario'); //сценарий для валид

		if(!$model || !$searchValue || !$value || !$attribute){
			Common::jsonError('Переданные параметры не верны.', true);
		}

		$model = AR::model($model)->find("{$searchField} =:searchValue", [':searchValue' => $searchValue]);
		if($scenario) $model->scenario = $scenario;
		
		$model->$attribute = $value;
		
		if(!$model->validate([$attribute])) {
			Common::jsonError($model->getErrors(), true);
		}
		else{
			if($model->save())
				Common::jsonSuccess(true);
			else
				Common::jsonError($model->getErrors(), true);
		}

		Common::jsonError('Не оредвиденная ошибка', true);
	}

}