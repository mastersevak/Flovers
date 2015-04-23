<?php

class m131130_120112_create_user_auth_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user_auth}}")){
			$this->dropTable("{{user_auth}}");
		}

		$this->createTable("{{user_auth}}", array(
			"id"                    => "int UNSIGNED AUTO_INCREMENT",
			"id_user"               => "int UNSIGNED",
			"service_name"          => "varchar(50) CHARACTER SET UTF8",
			"service_user_id"       => "varchar(50) CHARACTER SET UTF8",
			"service_user_name"     => "varchar(50) CHARACTER SET UTF8",
			"service_user_url"      => "varchar(255) CHARACTER SET UTF8",
			"service_user_pic"      => "varchar(255) CHARACTER SET UTF8",
			"service_user_email"    => "varchar(70) CHARACTER SET UTF8",
			"created"               => "datetime",
			"changed"               => "datetime",
			"id_creator"			=> "int UNSIGNED",
			"id_changer"			=> "int UNSIGNED",
			"PRIMARY KEY (id)",
			"KEY id_user (id_user)",
			"KEY service_name (service_name)",
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user_auth}}")){
			$this->dropTable("{{user_auth}}");
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