<?php

class SortableBehavior extends CActiveRecordBehavior
{

    /**
     * @var string  Field to store sorting
     */
    public $column = 'pos';

    public $pk = 'id';

    public function beforeFind($event)
    {
        $criteria = $this->owner->getDbCriteria();
        if (!$criteria->order){
            $alias = $this->owner->getTableAlias(); //чтобы не было такого (Integrity constraint violation: Column 'pos' in order clause is ambiguous.) при join запросах
            $criteria->order = "`{$alias}`.`{$this->column}` DESC";

        }

        parent::beforeFind($event);
    }


    public function beforeSave($event)
    {
        $model = $this->getOwner();
        $column = $this->column;
        if ($model->isNewRecord && empty($model->$column))
            $model->$column = Yii::app()->db->createCommand("SELECT MAX({$this->column}) FROM " . $model->tableName())->queryScalar() + 1;

        parent::beforeSave($event);
    }


    public function savePositions($ids, $start)
    {
        $priorities = array();
        foreach ($ids as $id)
            $priorities[$id] = $start--;

        $query = "UPDATE " . $this->getOwner()->tableName() . " SET {$this->column} = " . $this->_generateCase($priorities) . " WHERE {$this->pk} IN(" . implode(', ', $ids) . ")";

        Yii::app()->db->createCommand($query)->execute();

    }

    /**
     * Prepare table
     */
    public function prepareTable()
    {

        Yii::app()->db->createCommand("UPDATE " . $this->getOwner()->tableName() . " SET {$this->column} = {$this->pk}" )->execute();
    }


    private function _generateCase($priorities)
    {

        $result = "CASE {$this->pk}";
        foreach ($priorities as $k => $v)
            $result .= ' when "' . $k . '" then "' . $v . '"';
        return $result . ' END';
    }


}
