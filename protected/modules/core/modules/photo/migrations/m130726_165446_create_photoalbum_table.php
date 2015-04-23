<?php

class m130726_165446_create_photoalbum_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photoalbum}}")){
			$this->dropTable("{{photoalbum}}");
		}

		$this->createTable("{{photoalbum}}", array(
			"id"            => "int UNSIGNED AUTO_INCREMENT",
			"created"       => "datetime",
			"id_creator"    => "int UNSIGNED",
			"created"       => "datetime",
			"id_changer"    => "int UNSIGNED",
			"date"          => "datetime",
			"status"        => "tinyint(1)",
			"slug"          => "varchar(255) CHARACTER SET UTF8",
			"thumbnail"     => "int UNSIGNED",
			"pos"		    => "int UNSIGNED",
			"PRIMARY KEY (id)",
			"KEY slug (slug)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photoalbum_lang}}")){
			$this->dropTable("{{photoalbum_lang}}");
		}

		$this->createTable("{{photoalbum_lang}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"id_photoalbum"    => "int UNSIGNED",
			"language"         => "char(2) CHARACTER SET UTF8",
			"title"            => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_photoalbum (id_photoalbum)",
			"KEY language (language)",
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photoalbum}}")){
			$this->dropTable("{{photoalbum}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photoalbum_lang}}")){
			$this->dropTable("{{photoalbum_lang}}");
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