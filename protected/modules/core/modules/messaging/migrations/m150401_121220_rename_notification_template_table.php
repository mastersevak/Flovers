<?php

class m150401_121220_rename_notification_template_table extends EDbMigration
{
	public function up()
	{
		if(Yii::app()->db->getSchema()->getTable("{{notification_templates}}"))
			$this->renameTable("{{notification_templates}}", "{{notification_template}}");
	}

	public function down()
	{
		if(Yii::app()->db->getSchema()->getTable("{{notification_template}}"))
			$this->renameTable("{{notification_template}}", "{{notification_templates}}");
	}

}