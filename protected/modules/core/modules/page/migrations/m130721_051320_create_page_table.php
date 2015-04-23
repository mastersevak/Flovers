<?php

class m130721_051320_create_page_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{page}}")){
			$this->dropTable("{{page}}");
		}

		$this->createTable("{{page}}", array(
			"id"          => "int UNSIGNED AUTO_INCREMENT",
			"created"     => "datetime",
			"id_creator"  => "int UNSIGNED",
			"changed"     => "datetime",
			"id_changer"  => "int UNSIGNED",
			"status"      => "tinyint(1)",
			"slug"        => "varchar(255) CHARACTER SET UTF8",
			"route"	      => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY status (status)",
			"KEY slug (slug)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{page_lang}}")){
			$this->dropTable("{{page_lang}}");
		}

		$this->createTable("{{page_lang}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"id_page"          => "int UNSIGNED",
			"language"         => "char(2) CHARACTER SET UTF8",
			"title"            => "varchar(255) CHARACTER SET UTF8",
			"content"          => "text CHARACTER SET UTF8",
			"meta_title"       => "varchar(255) CHARACTER SET UTF8",
			"meta_keywords"    => "varchar(255) CHARACTER SET UTF8",
			"meta_description" => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_page (id_page)",
			"KEY language (language)",
			"KEY title (title)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{page}}")){
			$this->dropTable("{{page}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{page_lang}}")){
			$this->dropTable("{{page_lang}}");
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