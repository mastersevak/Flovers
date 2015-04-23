<?php

class m141209_130227_create_video_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{video}}")){
			$this->dropTable("{{video}}");
		}

		$this->createTable("{{video}}", array(
			"id"          => "int UNSIGNED AUTO_INCREMENT",
			"created"     => "datetime",
			"id_creator"  => "int UNSIGNED",
			"changed"     => "datetime",
			"id_changer"  => "int UNSIGNED",
			"date"		  => "datetime",
			"status"      => "tinyint(1)",
			"slug"        => "varchar(255) CHARACTER SET UTF8",
			"id_category" => "int UNSIGNED",
			"id_author"   => "int UNSIGNED",
			"video_url"	  => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY status (status)",
			"KEY id_category (id_category)",
			"KEY id_author (id_author)",
			"KEY slug (slug)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{video_lang}}")){
			$this->dropTable("{{video_lang}}");
		}

		$this->createTable("{{video_lang}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"id_video"          => "int UNSIGNED",
			"language"         => "char(2) CHARACTER SET UTF8",
			"title"            => "varchar(255) CHARACTER SET UTF8",
			"content"          => "text CHARACTER SET UTF8",
			"meta_title"       => "varchar(255) CHARACTER SET UTF8",
			"meta_keywords"    => "varchar(255) CHARACTER SET UTF8",
			"meta_description" => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_video (id_video)",
			"KEY language (language)",
			"KEY title (title)",
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{video}}")){
			$this->dropTable("{{video}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{video_lang}}")){
			$this->dropTable("{{video_lang}}");
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