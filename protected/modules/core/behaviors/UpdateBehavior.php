<?php
/**
 * Usage example in model:
 * public function relations()
 *   {
 *       return array(
 *           'related' => array(self::HAS_MANY, 'MyRelatedModel', 'relatedId', 'update'=>true)
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
class UpdateBehavior extends CActiveRecordBehavior
{
    private $_relatedModels = [];

    public $relations = [];

    /**
     * Событие происходящее при вызове функции loadModel()
     */
    public function onLoadModel($event){

       /* foreach($this->relations as $relation){
            
            if(isset($this->owner->$relation)){
                $this->_relatedModels[$relation] = $this->owner->$relation;  //тут проблематичный запрос
            }
           
        }*/

        $this->raiseEvent('onLoadModel', $event);
    }


    // public function beforeSave($event){
        
    //     if(parent::beforeSave($event)){

    //         foreach($this->relations as $relation){

    //             if($this->owner->isNewRecord){
    //                 $model = new EmployeeProfile;
    //             }
    //             else{
    //                 $model = $this->_relatedModels[$relation];
    //             }

    //             $profile->retired              = $this->retired;
    //             $profile->phone                = $this->phone;
    //             $profile->id_job               = $this->id_job;
    //             $profile->is_chief             = $this->is_chief;
    //             $profile->is_chief_assistant   = $this->is_chief_assistant;
    //             $profile->passport_gender      = $this->passport_gender;
    //             $profile->passport_birthday    = $this->passport_birthday;
    //             $profile->comment              = $this->comment;
    //             $profile->skype_name           = $this->skype_name;
    //             $profile->price_manager_color  = $this->price_manager_color;
    //             $profile->penalty_level        = $this->penalty_level ;
    //             $profile->penalty_approved     = $this->penalty_approved;
    //             $profile->penalty_level_1_date = $this->penalty_level_1_date;
    //             $profile->penalty_level_2_date = $this->penalty_level_2_date;
    //             $profile->penalty_level_3_date = $this->penalty_level_3_date;

    //             $this->profile = $profile;

    //         }


    //         return true;
    //     }

    //     return false;
    // }

    
}

?>