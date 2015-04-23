<?php

class m150120_085621_create_sms_notify_table extends EDbMigration
{
	public function up(){
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{sms_notify}}"))
			$this->dropTable("{{sms_notify}}");

		$this->createTable("{{sms_notify}}", array(
			"id"			=> "int UNSIGNED AUTO_INCREMENT",
			"created"		=> "datetime",
			"id_creator"	=> "int UNSIGNED",
			"changed"		=> "datetime",
			"id_changer"	=> "int UNSIGNED",
			"to"			=> "varchar(255) CHARACTER SET UTF8",
			"message"		=> "text CHARACTER SET UTF8",
			"status"		=> "int UNSIGNED",
			"PRIMARY KEY (id)"
			));

		if(Yii::app()->db->getSchema()->getTable("{{email_notification}}"))
			$this->renameTable("{{email_notification}}", "{{email_notify}}");
	}

	public function down(){
		if(Yii::app()->db->getSchema()->getTable("{{email_notify}}"))
			$this->renameTable("{{email_notify}}", "{{email_notification}}");

		if(Yii::app()->db->getSchema()->getTable("{{sms_notify}}"))
			$this->dropTable("{{sms_notify}}");
	}
}