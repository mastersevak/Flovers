<?php
/**
 * Usage example in model:
 * public function relations()
 *   {
 *       return array(
 *           'related' => array(self::HAS_MANY, 'MyRelatedModel', 'relatedId', 'updateBehavior'=>true)
 *       );
 *   }
 *
 *   public function behaviors()
 *   {
 *       return array(
 *           'CascadeUpdateBehavior' => array(
 *               'class' => 'application.components.behaviors.CascadeUpdateBehavior',
 *           )
 *       );
 *   }
 */
class CascadeUpdateBehavior extends CActiveRecordBehavior
{
      
    public function afterFind($event){

        $relations = array();
        $allRelations = $this->owner->relations();
        

        foreach ($allRelations as $relation => $options) {

            if(!empty($options['updateBehavior'])) {
                $relations[$relation] = $options;
            }
                
        }


        foreach($relations as $relation => $options)
        {

            $objects = $this->owner->getRelated($relation);



            if($objects !== null)
            {

                if(is_array($objects))
                {

                    /**
                     * @todo: реализовать обновление MANY_MANY, HAS_MANY
                     */
                }
                else
                { 
                    $attributes = $objects->attributes;
                    // unset($attributes['id']); //тут надо переделать, так как во первых pk, может быть не id, во вторых, другие поля в обих таблицах, тоже могут совпадать
                    $this->owner->setAttributes($attributes);
                }
            }
        }


        return true;
    } 


    public function beforeValidate($event){

        if(empty($_POST)) return true;

        $relations = array();
        $allRelations = $this->owner->relations();

        foreach ($allRelations as $relation => $options) {
            if(!empty($options['updateBehavior']) && $options['updateBehavior'])
                $relations[$relation] = $options;
        }


        foreach($relations as $relation => $options)
        {
            $objects = $this->owner->getRelated($relation);

            
            if($objects !== null) //update
            {
                if(is_array($objects))
                {

                    /**
                     * @todo: реализовать обновление MANY_MANY, HAS_MANY
                     */
                }
                else
                {
                    if(isset($_POST[get_class($this->owner)])){
                        $objects->setAttributes($_POST[get_class($this->owner)]);

                        $this->owner->withRelatedObjects[] = $relation;
                    }
                }
            }
            else{ //create
                $object = new $options[1];
                $object->setAttributes($_POST[get_class($this->owner)]);
                $this->owner->{$relation} = $object;

                $this->owner->withRelatedObjects[] = $relation;
            
            }
        }

        return true;
 
    }  
}

?>