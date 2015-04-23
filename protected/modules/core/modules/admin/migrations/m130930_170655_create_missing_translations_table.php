<?php

class m130930_170655_create_missing_translations_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{missing_translations}}")){
			$this->dropTable("{{missing_translations}}");
		}

		$this->createTable("{{missing_translations}}", array(
			"id"        => "int UNSIGNED AUTO_INCREMENT",
			"category"  => "varchar(255) CHARACTER SET UTF8",
			"language"  => "varchar(20) CHARACTER SET UTF8",
			"message"   => "varchar(255) CHARACTER SET UTF8",
			"page"		=> "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY category (category)",
			"KEY language (language)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{missing_translations}}")){
			$this->dropTable("{{missing_translations}}");
		}
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