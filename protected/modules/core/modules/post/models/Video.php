<?php

/**
* Video
*/
class Video extends Post
{
	public $thumbnail;

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName(){
		return "video";
	}

	public function rules(){
		return CMap::mergeArray(parent::rules(), [
			['video_url', 'safe'],
		]);
	}

	public function getBackUrl(){
		return app()->createUrl('core/post/video/update', ['id'=>$this->id]);
	}

	public function behaviors() {
		return CMap::mergeArray(parent::behaviors(), array(
			'ml' =>[
				'class' => 'core.behaviors.Ml',
				'langTableName' => 'video_lang',
				'langForeignKey' => 'id_video',
				'localizedAttributes' => array('title', 'content', 'meta_title', 'meta_keywords', 'meta_description'), //attributes of the model to be translated
			]
		));
	}

	public function getEmbed(){
		$search = "/(https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?(.+)?v=))([\w\-]{10,12}).*$/";
		$replace = "$3";
		$embedCode = preg_replace($search, $replace, $this->video_url);
		return $embedCode;
	}

}