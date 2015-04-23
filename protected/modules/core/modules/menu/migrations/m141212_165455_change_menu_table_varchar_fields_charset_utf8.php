<?php

class m141212_165455_change_menu_table_varchar_fields_charset_utf8 extends EDbMigration
{
	public function up()
	{
		$this->alterColumn("{{menu}}", "slug", "varchar(255) CHARACTER SET UTF8");
		$this->alterColumn("{{menu}}", "url", "varchar(255) CHARACTER SET UTF8");
		$this->alterColumn("{{menu}}", "name", "varchar(255) CHARACTER SET UTF8");
		$this->alterColumn("{{menu}}", "icon", "varchar(255) CHARACTER SET UTF8");
	}

	public function down()
	{
		$this->alterColumn("{{menu}}", "slug", "varchar(255)");
		$this->alterColumn("{{menu}}", "url", "varchar(255)");
		$this->alterColumn("{{menu}}", "name", "varchar(255)");
		$this->alterColumn("{{menu}}", "icon", "varchar(255)");
	}
}