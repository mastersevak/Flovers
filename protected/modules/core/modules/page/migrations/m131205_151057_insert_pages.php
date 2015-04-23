<?php

class m131205_151057_insert_pages extends EDbMigration
{
	public function up()
	{
		//INSERT articles page
		$this->insert('{{page}}', array('slug'=>'articles', 'status' => Page::STATUS_ACTIVE));
		$id = Yii::app()->db->getLastInsertId();
		foreach(param('languages') as $key=>$value){
			$this->insert('{{page_lang}}', array(
				'id_page' => $id,
				'language' => $key,
				'title' => 'Статьи'
			));
		}
		
	}

	public function down()
	{
		//DELETE articles page
		$slug = 'articles';
		$id = Yii::app()->db->createCommand('Select id from {{page}} where slug = "'.$slug.'"')->queryScalar();
		$this->delete('{{page}}', 'slug = :slug', array(':slug'=>$slug));
		$this->delete('{{page_lang}}', 'id_page = :id', array(':id'=>$id));
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}