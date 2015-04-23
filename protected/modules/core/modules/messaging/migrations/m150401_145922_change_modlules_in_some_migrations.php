<?php

class m150401_145922_change_modlules_in_some_migrations extends EDbMigration
{
	public function up()
	{
		$migrations = ['m140701_080556_create_notifications_templates_table', 'm150120_114510_create_notify_lang', 'm150401_121220_rename_notification_template_table', 'm150401_122046_rename_notification_template_table_lang'];
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'core.notify'], 'version = :version', [':version' => $migration]);
		}
	}

	public function down()
	{
		$migrations = ['m140701_080556_create_notifications_templates_table', 'm150120_114510_create_notify_lang', 'm150401_121220_rename_notification_template_table', 'm150401_122046_rename_notification_template_table_lang'];
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'admin'], 'version = :version', [':version' => $migration]);
		}
	}
}