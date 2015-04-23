<?php

class m150326_071710_change_modul_to_notify extends EDbMigration
{
	public function up()
	{
		$migrations = ['m141028_181843_create_sms_table', 'm150317_143935_create_mail_table', 'm150320_140207_add_attachments_column_in_mail_table', 'm150323_074916_add_error__column_in_mail_table'];
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'core.notify'], 'version = :version', [':version' => $migration]);
		}
	}

	public function down()
	{
		$migrations = ['m141028_181843_create_sms_table', 'm150317_143935_create_mail_table', 'm150320_140207_add_attachments_column_in_mail_table', 'm150323_074916_add_error__column_in_mail_table'];
		foreach ($migrations as $migration) {
			$this->update('{{tbl_migration}}', ['module' => 'core'], 'version = :version', [':version' => $migration]);
		}
	}
}