<?php

class m150325_130459_drop_notify_tables extends EDbMigration
{
	public function up()
	{
		$transaction = Yii::app()->db->beginTransaction();

		try{
			if(Yii::app()->db->getSchema()->getTable("{{sms_notify}}"))
				$this->dropTable("{{sms_notify}}");
			
			if(Yii::app()->db->getSchema()->getTable("{{email_notify}}"))
				$this->dropTable("{{email_notify}}");

			if(Yii::app()->db->getSchema()->getTable("{{log_notify}}"))
				$this->dropTable("{{log_notify}}");
			
			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}

	}

	public function down()
	{
		$transaction = Yii::app()->db->beginTransaction();

		try{
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
			
			$this->createTable("{{email_notify}}", array(
				"id"			=> "int UNSIGNED AUTO_INCREMENT",
				"created"		=> "datetime",
				"id_creator"	=> "int UNSIGNED",
				"changed"		=> "datetime",
				"id_changer"	=> "int UNSIGNED",
				"to"			=> "varchar(255) CHARACTER SET UTF8",
				"subject"		=> "varchar(255) CHARACTER SET UTF8",
				"message"		=> "text CHARACTER SET UTF8",
				"status"		=> "int UNSIGNED",
				"PRIMARY KEY (id)"
			));

			$this->createTable("{{log_notify}}", array(
				"id"			=> "int UNSIGNED AUTO_INCREMENT",
				"created"		=> "datetime",
				"id_creator"	=> "int UNSIGNED",
				"changed"		=> "datetime",
				"id_changer"	=> "int UNSIGNED",
				"level"			=> "varchar(255) CHARACTER SET UTF8",
				"category"		=> "varchar(255) CHARACTER SET UTF8",
				"message"		=> "text CHARACTER SET UTF8",
				"PRIMARY KEY (id)"
			));
			
			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}
	}
}