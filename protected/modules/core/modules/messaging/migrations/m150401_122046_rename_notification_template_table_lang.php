<?php

class m150401_122046_rename_notification_template_table_lang extends EDbMigration
{
	public function up()
	{
		if(Yii::app()->db->getSchema()->getTable("{{notification_templates_lang}}"))
			$this->renameTable("{{notification_templates_lang}}", "{{notification_template_lang}}");
	}

	public function down()
	{
		if(Yii::app()->db->getSchema()->getTable("{{notification_template_lang}}"))
			$this->renameTable("{{notification_template_lang}}", "{{notification_templates_lang}}");
	}
}