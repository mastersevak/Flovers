<?php 

/**
* Comments
*/
class Comments extends SWidget
{
	public $id;
	public $ownerModel;
	public $commentsRelation = 'comments';
	public $hasCommentsRelation = 'hascomments';
	public static $commentsRelationArray;
	public $addComment = 'addcomment'; // add comment url
	public $getComments = 'getcomments'; // get comments url

	public $beforeAdd, $afterAdd = 'function(result){}';

	public function run(){
		self::$commentsRelationArray[$this->id]['commentsRelation'] = $this->commentsRelation;
		self::$commentsRelationArray[$this->id]['hasCommentsRelation'] = $this->hasCommentsRelation;
		
		//Если аякс то не рисуем попап, а только заполняем массив с реляциями
		if(app()->request->isAjaxRequest) return false;
		
		$ownerModel = new $this->ownerModel;
		$commentsRelation = $ownerModel->metaData->relations[$this->commentsRelation];
		$commentsModelClassName = $commentsRelation->className;
		$commentsForeignKey = $commentsRelation->foreignKey;
		
		//init
		$model = new $commentsModelClassName;

		cs()->registerPackage('comments');
		cs()->registerScript('init-comments'.$this->id, 
				"$.fn.comments('init', {id: '{$this->id}'});", CClientScript::POS_READY);

		$this->render('index', compact('id', 'model', 'commentsForeignKey'));
	}

	public static function renderLink($idWidget, $model, $hasCommentsRelation = 'hascomments', $commentsRelation = 'comments', $htmlOptions = [], $additionalFields = false){
		$class = 'fa fa-comments' . ($model->$hasCommentsRelation ? ' has-comment' : '');

		if(isset($htmlOptions['class'])){
			$class = $class.' '.$htmlOptions['class'];
			unset($htmlOptions['class']);
		}

		if($additionalFields) $additionalFields = CJSON::encode($additionalFields);

		return CHtml::link('', '#', CMap::mergeArray([
			'onclick' => "$.fn.comments('open', '{$idWidget}', this); return false;",
			'class' => $class,
			'data-owner-modelname' => get_class($model),
			'data-comments-relation' => $commentsRelation,
			'data-id-owner' => $model->id,
			'data-show-on-hover' => false,
			'data-hover-url' => app()->controller->createUrl('getcommentsonhover'),
			'data-hover-title' => 'Комментарии к объекту',
			'data-additional-fields' => $additionalFields,
			'rel' => 'tooltip', 
			'title' => 'Комментарии'
		], $htmlOptions));
	}
}