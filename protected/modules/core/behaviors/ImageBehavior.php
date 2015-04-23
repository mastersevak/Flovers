<?php 


class ImageBehavior extends CActiveRecordBehavior{

    public $image; //картинка
    public $field; //поле для сохранения, ссылки на картинку
    public $relation = 'photo'; //реляция на таблицу Photo
    public $params; //массив с настройка для картинкок (например param('images/user') )
    public $placeholder;


    public function placeholder($size = 'thumb'){
        $placeholderPath = isset($this->params['placeholder']) ? 
                            $this->params['placeholder'] : 
                           app()->controller->module->assetsUrl.DS.'img'.DS.'placeholders';
        
        if($placeholderPath[0] != DS) $placeholderPath = DS . $placeholderPath;

        return $placeholderPath . DS.app()->theme->name. DS. 'nophoto-' . $size . '.png';
    }

    public function getImageUrl($size = 'thumb', $absoluteUrl = false){
        $photo = $this->owner->{$this->relation};

        if(!$photo){
            return $this->placeholder($size);
        }

        return $photo->getImageUrl($size, $absoluteUrl);
    }

    public function getThumbnail($size = 'thumb', $width = false, $height = false, $alt = '', $params = array(), $absoluteUrl = false, $refresh = false){

        $photo = $this->owner->{$this->relation};
        
        if(!$photo){
            $placeholder = $this->placeholder($size);

            $params = array_merge($params, array('width'=>($width? $width : 'auto'), 'height'=>($height? $height : 'auto')) );

            $prefix = $absoluteUrl ? request()->hostInfo : '';

            return CHtml::image($prefix.$placeholder, $alt, $params);
        }

        //вывод флеш банера
        if(File::getFileExtension($photo->filename) == 'swf'){

            $options = array();
            $options['src'] = $photo->getImageUrl($size, $absoluteUrl);
            
            $aInfo = getimagesize(substr($options['src'], 1)); //надо убрать спереди /
            list($iWidth, $iHeight) = $aInfo;

            $options['width'] = $iWidth;
            $options['height'] = $iHeight;

            $options['src'] .= ($this->owner->getUrl() ? '?clickTAG=' . $this->owner->getUrl() : '');

            return app()->controller->widget('ext.flash.EJqueryFlash', array(
                         'name'=>'flash'.$this->owner->id,
                         'htmlOptions'=>$options,
                    ), true);
        }
        
        //вывод фотки
        return $photo->getThumbnail($size, $width, $height, $alt, $params, $absoluteUrl, $refresh);
    }

    public function beforeSave($event){

        $this->ajaxUpload();

        return true;
    }


    public function ajaxUpload($autosave = false, $size = false){
        if(!$this->image || !$this->field) return true;
 
        //только для случаев, когда модель имеет одну картинку
        $file = CUploadedFile::getInstance($this->owner, $this->image);
        if($file == null) {
            //для случаев если uploader был не в cactiveform
            $file = CUploadedFile::getInstanceByName(get_class($this->owner).'_'.$this->image);
        }
        
        if(is_object($file) && get_class($file)==='CUploadedFile'){
            $filename = time();

            //аплоад, во временную папку
            $tmp = param('upload_tmp_folder'). $filename . '.' . $file->getExtensionName();
            File::checkPermissions(param('upload_tmp_folder'));
            
            $file->saveAs($tmp);
            
            $photo = new Photo;
            $photo->filename = $filename;

            $imgId = $photo->uploadImage($tmp, $this->params);

            if($imgId){

                if($this->owner->{$this->relation}){ //если фотография раньше была
                    $photo = $this->owner->{$this->relation};
                    $photo->delete();
                }

                //связка модели с фотографией
                $this->owner->{$this->field} = $imgId;

                if(!$this->owner->isNewRecord && $autosave){
                    $this->owner->saveAttributes(array($this->field));
                }
                
                //удаление из временой папки
                @unlink($tmp);

                if($size) return Yii::app()->baseUrl . DS 
                                . $this->params['path'] 
                                . $size . DS . $filename 
                                . '.' . $file->getExtensionName() 
                                . '?' . time();
            }
        }
    }


    public function getIsShowThumbnail(){
        return Cookie::get('show_images') == 1;
    }

}