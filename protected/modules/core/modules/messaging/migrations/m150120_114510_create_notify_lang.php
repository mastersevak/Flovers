<?php

class m150120_114510_create_notify_lang extends EDbMigration
{
	public function up()
	{

		$transaction = Yii::app()->db->beginTransaction();
		try{

			//delete table if exists
			if(Yii::app()->db->getSchema()->getTable("{{notification_templates_lang}}")){
				$this->dropTable("{{notification_templates_lang}}");
			}


			$this->createTable("{{notification_templates_lang}}", array(
				"id"		    => "int UNSIGNED AUTO_INCREMENT",
				"language"  	=> "char(2) CHARACTER SET UTF8",
				"id_template"	=> "int UNSIGNED",
				"title"		    => "varchar(255) CHARACTER SET UTF8",
				"subject"	    => "varchar(255) CHARACTER SET UTF8",
				"body"		    => "text CHARACTER SET UTF8",
				"PRIMARY KEY (id)"
				));

			if(Yii::app()->db->getSchema()->getTable("{{notification_templates}}")){
				$this->dropColumn("{{notification_templates}}", 'title');
				$this->dropColumn("{{notification_templates}}", 'subject');
				$this->dropColumn("{{notification_templates}}", 'body');

			}

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
			//delete table if exists
			if(Yii::app()->db->getSchema()->getTable("{{notification_templates_lang}}")){
				$this->dropTable("{{notification_templates_lang}}");
			}

			if(Yii::app()->db->getSchema()->getTable("{{notification_templates}}")){
				$this->addColumn("{{notification_templates}}", 'title', "varchar(255) CHARACTER SET UTF8");
				$this->addColumn("{{notification_templates}}", 'subject', "varchar(255) CHARACTER SET UTF8");
				$this->addColumn("{{notification_templates}}", 'body', "text CHARACTER SET UTF8");
				
			}

			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}
	}

}