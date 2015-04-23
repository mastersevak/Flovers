<?php

class m150320_140207_add_attachments_column_in_mail_table extends EDbMigration
{
	public function up()
	{
		$this->addColumn('{{mail}}', 'attachments', 'text CHARACTER SET UTF8');
	}

	public function down()
	{
		$this->dropColumn('{{mail}}', 'attachments');
	}
}