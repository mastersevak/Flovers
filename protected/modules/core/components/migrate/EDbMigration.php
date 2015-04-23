<?php

/**
 * EDbMigration
 *
 * @link http://www.yiiframework.com/extension/extended-database-migration/
 * @link http://www.yiiframework.com/doc/guide/1.1/en/database.migration
 * @author Carsten Brandt <mail@cebe.cc>
 * @version 0.7.1
 */
class EDbMigration extends CDbMigration
{
	/**
	 * @var EMigrateCommand
	 */
	private $migrateCommand;
	protected $interactive = true;

	/**
	 * @param EMigrateCommand $migrateCommand
	 */
	public function setCommand($migrateCommand)
	{
		$this->migrateCommand = $migrateCommand;
		$this->interactive = $migrateCommand->interactive;
	}

	/**
	 * @see CConsoleCommand::confirm()
	 * @param string $message
	 * @return bool
	 */
	public function confirm($message)
	{
		if (!$this->interactive) {
			return true;
		}
		return $this->migrateCommand->confirm($message);
	}

	/**
	 * @see CConsoleCommand::prompt()
	 * @param string $message
	 * @param mixed  $defaultValue will be returned when interactive is false
	 * @return string
	 */
	public function prompt($message, $defaultValue)
	{
		if (!$this->interactive) {
			return $defaultValue;
		}
		return $this->migrateCommand->prompt($message);
	}

	/**
	 * функции проверки существования таблицы или колонки
	 */
	protected function tableExists($tableName){
		return (bool)Yii::app()->db->schema->getTable($tableName);
	}

	protected function columnExists($tableName, $column){
		$columns = array_flip(Yii::app()->db->schema->getTable($tableName)->getColumnNames());
		return isset($columns[$column]);
	}


}
