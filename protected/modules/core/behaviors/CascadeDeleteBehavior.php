<?php
/**
 * Usage example in model:
 * public function relations()
 *   {
 *       return array(
 *           'related' => array(self::HAS_MANY, 'MyRelatedModel', 'relatedId', 'deleteBehavior'=>true)
 *       );
 *   }
 *
 *   public function behaviors()
 *   {
 *       return array(
 *           'CascadeDeleteBehavior' => array(
 *               'class' => 'application.components.behaviors.CascadeDeleteBehavior',
 *           )
 *       );
 *   }
 */
class CascadeDeleteBehavior extends CActiveRecordBehavior
{
      
    public function beforeDelete($event)
    {

        $relations = array();
        $allRelations = $this->owner->relations();

        foreach ($allRelations as $relation => $options) {
            if(!empty($options['deleteBehavior']) && $options['deleteBehavior'])
                $relations[$relation] = $options;
        }

        foreach($relations as $relation => $options)
        {
            //MANY_MANY
            if($options[0] == AR::MANY_MANY){
                /**
                 * @todo Доделать для связи MANY_MANY
                 */
                // $behaviors = $this->owner->behaviors(); 

               
                    // $info = array();
                    // $info['key'] = $relation;

                    // if (preg_match('/^(.+)\((.+)\s*,\s*(.+)\)$/s', $options[2], $pocks)) 
                    // {
                    //     $info['m2mTable'] = $pocks[1];
                    //     $info['m2mThisField'] = $pocks[2];
                    //     $info['m2mForeignField'] = $pocks[3];
                    // }
                    // else 
                    // {
                    //     $info['m2mTable'] = $options[2];
                    //     $info['m2mThisField'] = $this->owner->tableSchema->PrimaryKey;
                    //     $info['m2mForeignField'] = CActiveRecord::model($options[1])->tableSchema->primaryKey;
                    // }

                    // //удаление записи из главной таблицы
                    // if(isset($options['deleteSource']) && $options['deleteSource']){
                    //     $objects = $this->owner->$relation;
                    //     foreach($objects as $one){
                    //         $one->delete();
                    //     }
                        
                    // }

                    // //удаление записи из вспомогательной таблицы
                    // $deleteQry = sprintf("delete ignore from %s where %s = '%s'",
                    //         $info['m2mTable'],
                    //         $info['m2mThisField'],
                    //         $this->owner->{$this->owner->tableSchema->primaryKey}
                    //     );

                    // Yii::app()->db->createCommand($deleteQry)->execute();

            }
            else{
                $objects = $this->owner->getRelated($relation);
            
                if($objects !== null)
                {
                    if(is_array($objects))
                    {

                        foreach($objects as $object)
                        {
                            if(isset($options['empty']))
                                $object->updateByPk($object->primaryKey, array($options['empty']=>null));
                            else 
                                $object->delete();
                        }
                    }
                    else
                    {
                        if(isset($options['empty']))
                             $objects->updateByPk($object->primaryKey, array($options['empty']=>null));
                        else 
                            $objects->delete();
                    }
                }
            }
        }
        
        return true;
    }    
}

?>