<?php

class m140808_103622_change_settings_table extends EDbMigration
{
	public function up()
	{
		$this->addColumn("{{settings}}", "created", "datetime");
		$this->addColumn("{{settings}}", "changed", "datetime");
	}

	public function down()
	{
		$this->dropColumn("{{settings}}", "created");
		$this->dropColumn("{{settings}}", "changed");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}