<?
class LogDb extends CDbLogRoute
{
 
    protected function createLogTable($db,$tableName)
    {
        $db->createCommand()->createTable($tableName, array(
            'id'        => 'pk',
            'level'     => 'varchar(128) CHARACTER SET UTF8',
            'category'  => 'varchar(128) CHARACTER SET UTF8',
            'logtime'   => 'integer', 
            'message'   => 'text CHARACTER SET UTF8',
        ));
    }
    protected function processLogs($logs)
    {
        $command = $this->getDbConnection()->createCommand();
       
        foreach($logs as $log){
            $command->insert($this->logTableName,array(
                'level'     =>  $log[1],
                'category'  =>  $log[2],
                'logtime'   =>  (int)$log[3],
                'message'   =>  $log[0],
            ));
        }
    }
 
}