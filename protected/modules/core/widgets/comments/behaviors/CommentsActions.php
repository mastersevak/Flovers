<?php 

/**
* CommentsActions
*/
class CommentsActions extends CBehavior
{

	/**
	 * Добавить комментарий
	 */
	public function actionAddComment(){
		$commentsModelName = request()->getParam('commentsModelName');
		$commentsForeignKey = request()->getParam('commentsForeignKey');
		$idOwner = request()->getParam('idOwner');
		$additionalFields = request()->getParam('additionalFields');

		$model = $this->owner->loadModel($commentsModelName);
		$this->owner->performAjaxValidation($model);

		$model->id_user = user()->id;
		$model->comment = request()->getParam('comment');
		$model->$commentsForeignKey = $idOwner;
		$model->date = date('y-m-d H:i:s');

		if($additionalFields){
			$additionalFields = CJSON::decode($additionalFields);
			foreach($additionalFields as $field => $value)
				$model->$field = $value;
		}
	
		if($model->save())
			Common::jsonSuccess(true, ['data' => $model->render()]);
		else
			Common::jsonError("Ошибка при сохранении");
	}

	/**
	 * Вернуть все комментарии для указанной реляции, в отформатированном виде
	 * @return JSON - список комментариев
	 */
	public function actionGetComments(){
		$ownerModelName = request()->getParam('ownerModelName');
		$idOwner = request()->getParam('idOwner');
		$commentsRelation = request()->getParam('commentsRelation');
		$additionalFields = request()->getParam('additionalFields');

		$model = $this->owner->loadModel($ownerModelName, $idOwner);
		$comments = $model->$commentsRelation;

		if(!$comments && $additionalFields){
			$additionalFields = CJSON::decode($additionalFields);
			$criteria = new SDbCriteria;
			foreach($additionalFields as $field => $value)
				$criteria->compare($field, $value);

			$comments = ConsolidatedReportMonthlyComment::model()->findAll($criteria);
		}
	
		$result = [];
		foreach($comments as $comment){
			$result[] = $comment->render();
		}

		echo CJSON::encode($result);
	}

	/**
	 * Возвращает комментарии к заказу
	 */
	public function actionGetCommentsOnHover(){
		$ownerModelName = request()->getParam('ownerModelName');
		$idOwner = request()->getParam('idOwner');
		$commentsRelation = request()->getParam('commentsRelation');
		$title = request()->getParam('title') ? request()->getParam('title').$idOwner : '';

		if(!$idOwner) Common::jsonSuccess(true, ['success' => false, 'error' => 'Произошла ошибка при запросе']);

		$resultComments = '';

		$ownerModel = $this->owner->loadModel($ownerModelName, $idOwner);
		$comments = $ownerModel->$commentsRelation;
		
		$resultComments .= CHtml::openTag('div', ['class'=>'comment-block-item']);
			$resultComments .= CHtml::tag('h4', ['class'=>'fsize16 m0'], $title);
			
			if(!$comments)
				$resultComments .= CHtml::tag('div', ['class'=>'comment comment-no-result tcenter p5 fsize13 clearfix'], 'Нет результатов.');
			else{
				foreach($comments as $comment){
					$ownerComment = $comment->render();
					$resultComments .= CHtml::openTag('div', ['class'=>'comment p5 fsize13 clearfix'])
								.CHtml::tag('span', ['class' => 'c-black fl w360'], nl2br($comment->comment))
								.CHtml::tag('p', ['class'=>'c-dark-gray m0 fr w140'], date('d/m H:i', strtotime($comment->date)).', '.($comment->user ? $comment->user->username : ''))
							.CHtml::closeTag('div');
				}
			}
					
			
		$resultComments .= CHtml::closeTag('div');

		if(!$resultComments) Common::jsonSuccess(true, ['success' => false, 'error' => 'Ошибка при обработке комментариев']);
			
		Common::jsonSuccess(true, compact('resultComments'));
	}
}