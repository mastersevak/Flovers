<?php

class m150403_075541_change_modules_in_messaging_migrations extends EDbMigration
{
	public function up()
	{
		$migrations = [
		'm140701_080556_create_notifications_templates_table',
		'm141028_181843_create_sms_table', 
		'm150120_114510_create_notify_lang', 
		'm150317_143935_create_mail_table',
		'm150320_140207_add_attachments_column_in_mail_table',
		'm150323_074916_add_error__column_in_mail_table',
		'm150325_112923_add_log_table_in_mail_table',
		'm150326_071710_change_modul_to_notify',
		'm150401_121220_rename_notification_template_table',
		'm150401_122046_rename_notification_template_table_lang',
		'm150401_145922_change_modlules_in_some_migrations',
		'm150402_131633_rename_slug_column_in_notification_tamplate_table'];
		
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'core.notify'], 'version = :version', [':version' => $migration]);
		}
	}

	public function down()
	{
		$migrations = [
		'm140701_080556_create_notifications_templates_table',
		'm141028_181843_create_sms_table', 
		'm150120_114510_create_notify_lang', 
		'm150317_143935_create_mail_table',
		'm150320_140207_add_attachments_column_in_mail_table',
		'm150323_074916_add_error__column_in_mail_table',
		'm150325_112923_add_log_table_in_mail_table',
		'm150326_071710_change_modul_to_notify',
		'm150401_121220_rename_notification_template_table',
		'm150401_122046_rename_notification_template_table_lang',
		'm150401_145922_change_modlules_in_some_migrations',
		'm150402_131633_rename_slug_column_in_notification_tamplate_table'];
		
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'core.notify'], 'version = :version', [':version' => $migration]);
		}
	}

}