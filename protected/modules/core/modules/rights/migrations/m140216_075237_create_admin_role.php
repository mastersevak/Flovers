<?php

class m140216_075237_create_admin_role extends EDbMigration
{
	public function up()
	{
		$auth=Yii::app()->authManager;
		$role=$auth->createRole('admin', 'Администратор');
		 
		$auth->assign('admin', 2);
	}

	public function down()
	{
		$auth=Yii::app()->authManager;
		 
		$auth->revoke('admin', 2);
		$auth->removeAuthItem('admin');
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