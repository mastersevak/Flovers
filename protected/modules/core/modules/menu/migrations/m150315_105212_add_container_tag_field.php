<?php

class m150315_105212_add_container_tag_field extends EDbMigration
{
	
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		if(Yii::app()->db->getSchema()->getTable("{{menu}}")){
			$this->addColumn("{{menu}}", "containerTag", "varchar(10)");
			$this->addColumn("{{menu}}", "itemTag", "varchar(10)");
		}


	}

	public function safeDown()
	{
		if(Yii::app()->db->getSchema()->getTable("{{menu}}")){
			$this->dropColumn("{{menu}}", "containerTag");
			$this->dropColumn("{{menu}}", "itemTag");
		}
	}
	
}