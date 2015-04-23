<?php

class m150120_065615_rename_table_email_history extends EDbMigration
{
	public function up(){
		$this->renameTable("{{email_history}}", "{{email_notification}}");
	}

	public function down(){
		$this->renameTable("{{email_notification}}", "{{email_history}}");
	}
}