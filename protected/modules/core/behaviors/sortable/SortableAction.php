<?php

class SortableAction extends CAction
{

    public $column = 'pos';
    public $model = null;
    public $field;
    public $condition;

    public function run()
    {
        if (isset($_POST['ids']) && is_array($_POST['ids']))
        {
            if ($this->model === null)
                throw new CException('Не указана таблица');

            if(is_string($this->model)){
                $this->model = new $this->model;
            }

            $primaryKey = $this->model->primaryKey() ?  $this->model->primaryKey() : 'id';

            if($this->field){ //например для случае с таблицей shortcut
                foreach($_POST['ids'] as $id)
                    $ids[] = Yii::app()->db->createCommand("SELECT id FROM " . 
                            $this->model->tableName() . " WHERE {$this->field} = {$id}" . 
                                    ($this->condition ? " and " . $this->condition : "") )->queryScalar();
            }
            else {
                $ids = $_POST['ids'];
            }

            $max = (int) Yii::app()->db->createCommand("SELECT MAX({$this->column}) FROM " . 
                        $this->model->tableName() . " WHERE {$primaryKey} IN(" . 
                            implode(', ', $ids) . ")" )->queryScalar();

            if (!is_numeric($max) || $max == 0)
                $this->model->prepareTable();

            $this->model->savePositions($ids, $max);

            echo 'true'; //нужно для повторной активации плагина для модуля фоток
        }

    }


}
