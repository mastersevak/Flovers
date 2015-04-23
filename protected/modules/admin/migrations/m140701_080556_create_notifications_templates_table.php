<?php

class m140701_080556_create_notifications_templates_table extends EDbMigration
{
	public function safeUp()
	{	
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{notification_templates}}")){
			$this->dropTable("{{notification_templates}}");
		}

		$this->createTable("{{notification_templates}}", array(
			"id"		=> "int UNSIGNED AUTO_INCREMENT",
			"created"	=> "datetime",
			"changed"	=> "datetime",
			"type"		=> "tinyint(1)",
			"slug"		=> "varchar(255) CHARACTER SET UTF8",
			"title"		=> "varchar(255) CHARACTER SET UTF8",
			"subject"	=> "varchar(255) CHARACTER SET UTF8",
			"body"		=> "text CHARACTER SET UTF8",
			"PRIMARY KEY (id)"
			));
	}

	public function safeDown()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{notification_templates}}")){
			$this->dropTable("{{notification_templates}}");
		}
	}
}