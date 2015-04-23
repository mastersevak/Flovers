<?php 


/**
* DefaultController - контроллер для работы с загрузкой, и удалением фотографий
*/
class DefaultController extends FController
{
    public $model = 'Photo';

    public function filters(){
        return CMap::mergeArray(parent::filters(), array(
            'postOnly',
        ));
    }

    public function actions(){

        return array(
            'sorting' =>  array(
                'class' => 'core.behaviors.sortable.SortableAction',
                'model' => $this->model
            )
        );
    } 

	/**
	 * загрузка фоторгафий и привязка к модели
	 */
	public function actionUpload($modelName, $modelId, $params){

        ini_set('post_max_size', param('upload_max_filesize'));
        ini_set('upload_max_filesize', param('upload_max_filesize'));

        $tmpFolder = param('upload_tmp_folder');// folder for uploaded files

        Yii::import("core.extensions.EFineUploader.qqFileUploader"); 
        $uploader = new qqFileUploader();
        $uploader->allowedExtensions = param('upload_allowed_extensions');
        $uploader->sizeLimit = param('upload_size_limit'); //maximum file size in bytes
        
        //аплоад, во временную папку
        $result = $uploader->handleUpload($tmpFolder); 
        $result['filename'] = $uploader->getUploadName();
        
        //путь к временному файлу
        $tmpFile = $tmpFolder. $result['filename'];
        
        $photo = new Photo;
        $photo->filename = String::randomString(12);

        $params = param('images/'.$params);
        $params['path'] .= $modelId . DS;

        $imgId = $photo->uploadImage($tmpFile, $params);

        if($imgId){

            //связка модели с фотографией
            $related = new RelatedPhoto;
            $related->id_photo = $imgId;
            $related->id_model = $modelId;
            $related->model = $modelName;
            $related->save();
            
            //удаление из временой папки
            @unlink($tmpFile);
        }

        //return info
        $result['filename'] = $photo->filename;
        $result['path'] = bu().$params['path'].'thumb'.DS;
        $result['id'] = $imgId;

        echo CJSON::encode($result);
	}

	/**
	 * удаление фоторгафий и отвязка от модели
	 */
	public function actionDelete(){
        
        $id = Yii::app()->request->getParam('id');      

        $photo = $this->loadModel($this->model, $id);

        $photo->delete();
        
        echo CJSON::encode(array('success'=>true));
        
	}

    //для ajax удаления аватарки
    public function actionDeleteThumbnail(){
        $result = array('success'=>false);

        $id = request()->getParam('id');
        $model = request()->getParam('model', false);

        if(!$model){
            $model = $this->loadModel($model, $id);
        }
        else {
            $model = $this->loadModel($model, $id);
        }
        
        if($model->photo && $model->photo->delete()){
            $size = request()->getParam('size');

            $behaviors = $model->behaviors();
            $fieldName = $behaviors['imageBehavior']['field'];
            
            $model->$fieldName = null;
            $model->saveAttributes(array($fieldName));

            $result['placeholder'] = $model->placeholder($size);
            $result['success'] = true;
        }

        echo CJSON::encode($result);
    }

	/**
	 * изменение названия фоток
	 */
	public function actionChangeTitle(){
            
        $id = Yii::app()->request->getParam('id');  
        $val = Yii::app()->request->getParam('val');  

        $photo = $this->loadModel($this->model, $id);

        $photo->title = $val;

        if($photo->validate()) {
            $photo->save();
            echo $photo->title;
        }
        else echo "false";
	}

    /**
     * Автоматическая загрузка картинки после ее выбора
     */
    public function actionAjaxUpload($model, $id){
        //потом поменять на fineuploader, добавив невидимую кнопку
        $model = $model::model()->findByPk($id);

        $size = Yii::app()->request->getParam('size', 'thumb');
        $result = array('success'=>false);

        if($model){
            $result['success'] = true;
            $result['src'] = $model->ajaxUpload(true, $size);
            echo CJSON::encode($result);
        }
    }

    /**
     * Поворот картинки на 90 градусов
     */
    public function actionRotate($model, $id){

        if($model != "Photo")
            $model = $model::model()->with('photo')->findByPk($id);
        else
            $model = $model::model()->findByPk($id);

        $size = Yii::app()->request->getParam('size', 'large');
        $side = Yii::app()->request->getParam('side');

        $result = array('success'=>false);

        if($model){
            if(get_class($model) != "Photo"){
                $result['src'] = $model->photo->rotate($side, $size);
            }
            else{
                $result['src'] = $model->rotate($side, $size);
            }
            $result['success'] = true;
            
            echo CJSON::encode($result);
        }
    }

    public function actionCrop($id, $model){
        
        $action = Yii::app()->request->getParam('action');
        
        switch($action){
            case 'init':
                $this->initCropContainer($id, $model);
                break;
            case 'crop':
                $this->applyCrop($id, $model);
        } 
    }


    /**
     * Показ окна обрезки изображения
     */
    public function initCropContainer($id, $model){
        
        $result = array('success'=>false);

        if($model != "Photo") //для простых аватарок    
            $model = $model::model()->with('photo')->findByPk($id);
        else
            $model = $model::model()->findByPk($id);

        if(!$model){
            $result['message'] = 'Модель не найдена!';
            echo CJSON::encode($result);
            echo Yii::app()->end();
        }

        if(get_class($model) != "Photo") //для простых аватарок   
            $originalImage = $model->photo->getImagePath('original');
        else 
            $originalImage = $model->getImagePath('original');

        if(!file_exists($originalImage)) {
            $result['message'] = 'Для данного изображения отсутствует оригинальная копия!';
            echo CJSON::encode($result);
            echo Yii::app()->end();
        }
       
        $images = $this->getCropSizes($model);
        
        $result['small'] = $images['images'];
        $result['sizes'] = $images['sizes'];
        
        $images = array();

        foreach($result['sizes'] as $size){
            $img = $this->getCrop($model, $size);
            $images[] = array('options' => $img['options'], 'image' => $img['image'], 'fields' => $img['fields']);
        }
        
        $result['big'] = $images;

        $result['success'] = true;

        echo CJSON::encode($result);

        echo Yii::app()->end();

    }

    public function applyCrop($id, $model){
        $result = array('success'=>false);

        $size = Yii::app()->request->getParam('size');

        if(isset($_POST['data'])){
            if($model != "Photo")
                $model = $model::model()->with('photo')->findByPk($id);
            else 
                $model = $model::model()->findByPk($id);
            
            if($model){
                $options = $_POST['data'];
                
                if(get_class($model) != "Photo"){
                    $behaviors = $model->behaviors();
                    $params = $behaviors['imageBehavior']['params']['sizes'];

                    $model->photo->crop($options, $params);
                }
                else{
                    $params = param('images/'.strtolower($model->related->model).'Photo/sizes');
                    $model->crop($options, $params);
                }

                $result['success'] = true;

                $result['src'] = $model->getImageUrl($size).'?'.time();
            }
            else{
                $result['message'] = 'Модель не найдена!';
            }

        }

        echo CJSON::encode($result);
    }

    /**
     * Функция рисует маленькие картинки, для вывода окна обрезки
     */
    public function getCropSizes(&$model){
        
        $sizes = $this->getSizes($model);

        $images = '';
        $_sizes = array();
        
        foreach($sizes as $size=>$options){
            if($size == 'original') continue; //пропустить оригинал

            if(get_class($model) != "Photo")
                $img = getimagesize($model->photo->getImagePath($size));
            else
                $img = getimagesize($model->getImagePath($size));
            
            $imgWidth = $img[0];
            $imgHeight = $img[1];

            $_sizes[] = $size;
            $height = $height = ($options['height'] < 100 ? floor($options['height']) : 100);
            $width = floor($imgWidth * $height / $imgHeight);
            
            $images .= '<div style="overflow: hidden; width:' . $width . 'px; height:' . $height . 'px">'
                        .$model->getThumbnail($size, $width, $height, 'small size', array(
                            'data-size'=>$size, 
                            'data-output-width'=>isset($options['width']) ? $options['width'] : 0, 
                            'data-output-height'=>isset($options['height']) ? $options['height'] : 0), 
                        false, true).'</div>';
        }

        return array('images'=>$images, 'sizes'=>$_sizes);
    }

    public function getCrop(&$model, $size){
        //вывод конкретного размера
        $params = $this->getSizes($model, $size);

        $maxWidth = 800;
        $maxHeight = 700;

        if(get_class($model) != "Photo")
            $img = getimagesize($model->photo->getImagePath('original'));
        else
            $img = getimagesize($model->getImagePath('original'));

        $imgWidth = $img[0];
        $imgHeight = $img[1];

        $crop = isset($params['crop']);

        $result = Photo::prepareSizes($img, $params);

        if($crop){
            if($result[0] == null){

                $w = $imgHeight * $params['width'] / $params['height'];
                $h = $imgHeight;
            }
            elseif($result[1] == null){
                $w = $imgWidth;
                $h = $imgWidth * $params['height'] / $params['width'];
            }
        }
        else{
            $w = $imgWidth;
            $h = $imgHeight;
        }

        $options = array(
            'setSelect' => array(($imgWidth - $w) / 2, ($imgHeight - $h) / 2, $w, $h),
            'boxWidth'  => $maxWidth,
            'boxHeight' => $maxHeight,
            // 'onRelease' => "js:function() {ejcrop_cancelCrop(this);}",
        );

        if($crop){
            $options['aspectRatio'] = $params['width'] / $params['height'];
        }

        $fields = CHtml::hiddenField($size.'_x', '')
                 .CHtml::hiddenField($size.'_y', '')
                 .CHtml::hiddenField($size.'_x2', '')
                 .CHtml::hiddenField($size.'_y2', '')
                 .CHtml::hiddenField($size.'_w', '')
                 .CHtml::hiddenField($size.'_h', '');

        $image = '<div class="big" style="width:0; height:0; overflow:hidden">'
                 .$model->getThumbnail('original', null, null, false, array('data-size'=>$size))
                 .$fields
                 .'</div>';

        return array('options' => $options, 
                     'image' => $image,
                     'fields' => $fields );
    }

    //получает размеры для картинки
    public function getSizes(&$model, $size = false){
        
        if(get_class($model) != "Photo"){
            $behaviors = $model->behaviors();
            $sizes = $behaviors['imageBehavior']['params']['sizes'];
        }
        else{
            $related = $model->related;
            if($related){
                $modelName = $related->model;
                $sizes = param('images/'.strtolower($modelName).'Photo/sizes');
            }
        }
        
        if($size) return $sizes[$size];

        return $sizes;
    }

}

