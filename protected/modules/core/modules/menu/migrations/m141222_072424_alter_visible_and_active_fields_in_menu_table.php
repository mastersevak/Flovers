<?php

class m141222_072424_alter_visible_and_active_fields_in_menu_table extends EDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->alterColumn("{{menu}}", "visible", "varchar(500) CHARACTER SET UTF8");
		$this->alterColumn("{{menu}}", "active", "varchar(500) CHARACTER SET UTF8");
		$this->alterColumn("{{menu}}", "icon", "varchar(500) CHARACTER SET UTF8");
		$this->renameColumn('{{menu}}', 'icon', 'items');
	}

	public function safeDown()
	{
		$this->alterColumn("{{menu}}", "visible", "tinyint(1)");
		$this->alterColumn("{{menu}}", "active", "tinyint(1)");
		$this->alterColumn("{{menu}}", "items", "tinyint(1)");
		$this->renameColumn('{{menu}}', 'items', 'icon');
	}
}