<?php

class m150402_131633_rename_slug_column_in_notification_tamplate_table extends EDbMigration
{
	public function up()
	{
		$this->renameColumn('{{notification_template}}', 'slug', 'key');
	}

	public function down()
	{
		$this->renameColumn('{{notification_template}}', 'key', 'slug');
	}
}