<?php

class m130721_054742_create_block_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{block}}")){
			$this->dropTable("{{block}}");
		}

		$this->createTable("{{block}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"created"          => "datetime",
			"id_creator"       => "int UNSIGNED",
			"changed"          => "datetime",
			"id_changer"       => "int UNSIGNED",
			"slug"             => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY slug (slug)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{block_lang}}")){
			$this->dropTable("{{block_lang}}");
		}

		$this->createTable("{{block_lang}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"id_block"         => "int UNSIGNED",
			"language"         => "char(2) CHARACTER SET UTF8",
			"title"            => "varchar(255) CHARACTER SET UTF8",
			"content"          => "text CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_block (id_block)",
			"KEY language (language)",
			"KEY title (title)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{block}}")){
			$this->dropTable("{{block}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{block_lang}}")){
			$this->dropTable("{{block_lang}}");
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