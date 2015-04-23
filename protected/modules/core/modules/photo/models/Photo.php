<?php 

Yii::import('core.extensions.EWideImage.*');
/**
* Photo - модель
* @property integer     $id         - id шаблона
* @property date        $created    - создано
* @property integer     $id_creator - Кто создал
* @property string      $path       - путь
* @property string      $filename   - имя файла 
* @property string      $size       - размер
* @property string      $title      - название
* @property integer     $pos        - позиция
* @property integer     $status     - статус
*  
*/
class Photo extends AR
{
    const IMAGE_LIGHTEN = 1;
    const IMAGE_DARKEN = 2;

    public $image; //переменная для хранения картинки

    public $types;

    public $class = __CLASS__;

    public $linkedField = 'id_photo';

    public function init(){
        $this->types = param('upload_allowed_extensions');
        $this->types[] = 'swf';
    }

    public static function model($classname = __CLASS__){
        return parent::model($classname);
    }

    // отдаём соединение, описанное в компоненте db_old
    public function getDbConnection(){
        return Yii::app()->db;
    }
 
    // возвращаем имя таблицы вместе с именем БД
    public function tableName(){
         return 'photo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        
        return array(
            array('filename, path, sizes', 'filter', 'filter'=>'trim'),
            array('filename, path, sizes', 'length', 'max'=>255),
            
            //type
            array('image', 'file', 'types'=>implode(', ', $this->types), 'allowEmpty' => true),

            array('title', 'filter', 'filter'=>'trim'),
            array('title', 'length', 'max'=>255),
            array('title', 'filter', 'filter' => [$this->purifier, 'purify'] ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // 'lang' => array(self::HAS_ONE, 'PhotoLang', 'id_photo'),
            'related' => array(self::HAS_ONE, 'RelatedPhoto', $this->linkedField, 'deleteBehavior'=>true)
        );
    }

    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), array(
            'dateBehavior' => array(
                'class'           => 'DateBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'changed',
            ),
            'sortable' => array(
                'class' => 'core.behaviors.sortable.SortableBehavior',
                'pk' => 'id',
            ),
        ));
    }

    /**
     * Загрузка изображения со всеми размерами и сохранением в базу
     *
     * Пример использования
     * 
     * Loads an image from a file, URL, HTML input file field, binary string, or a valid image handle.
     * Currently supported formats: PNG, GIF, JPG, BMP, TGA, GD, GD2.
     *
     * $image = 'http://url/image.png'           // image URL
     * $image = '/path/to/image.png'             // local file path
     * $image = 'img'                            // upload field name
     * $image = imagecreatetruecolor(10, 10)     // a GD resource
     * $image = $image_data                      // binary string containing image data  * 
     * 
     *     $photo = new Photo;
     *     $photo->filename = time() . '.' . File::getFileExtension($image);
     *     $photo->title = "тестовая запись";
     *     $photo->uploadImage($image, param('images/news'));
     * @return 
     */
    public function uploadImage($image, $params, $returnField = 'id', $path = false){

        $ext = File::getFileExtension($image);

        if(!in_array($ext, $this->types)){
            return false;
        }
        if(!$this->filename){
            $this->filename = time() . '.' . $ext;
        }
        else {
            $this->filename .= '.' . $ext;
        }

        if(!$path)
            $path = $params['path'];

        $sizes = array();
        
        foreach($params['sizes'] as $size=>$param){
            if(isset($param['queue']) && $param['queue']){ //бросить обработку данного размера в очередь
                
                if(Yii::app()->amqp->loaded)
                    Yii::app()->amqp->exchange('process-images')->publish(
                        serialize(['id'=>$model->id]), 'resize.images');
            }
            else //обработать прямо сейчас
                $result = self::upload($image, $path.$size.DS, $param, $this->filename);

            if(!$result){
                throw new CHttpException(404, "Файл для загрузки не найден ({$image})");
            }

            $sizes[] = $size;
        }

        //сохранение записи о изображении
        $this->path    = $path;
        $this->created = time();  
        $this->sizes   = implode(',', $sizes);  
        $this->status  = self::STATUS_ACTIVE;

        if($this->save()){
            return $this->$returnField;
        }

        return false;
    }

	/**
	 * [upload description]
     * 
     * Пример использования
     * Photo::upload($imageForUpload, array(
     *                 'path'=>'storage/.tmp/', 
     *                 'width'=>500, 
     *                 'height'=>300, 
     *                 'crop'=>true, 
     *                 'watermark'=>true, 
     *                 'watermark_image'=>$watermarkImage));
	 *
     *  @return string filename
	 */
	public static function upload($url, $path, $params, $newName = false){
        
        $webroot = '';

        $webroot = (strpos($url, 'http') == 0 ? '' : Yii::getPathOfAlias('webroot').DS); //эта переменная нужна для правильной обработки путей из CLI

		//увеличить лимит для загрузки
		self::addLimits(param('upload_max_filesize'));
		
        //проверка на существование файла
        if(!$url || !File::fileExists($webroot.$url)){
            return false;
        }
		$ext = File::getFileExtension($webroot.$url);
        //get new filename
        $filename = $newName ? $newName : time().'.'.$ext;

        //swf file
        if($ext == 'swf' || ($ext == 'gif' && EWideImage::is_animated($webroot.$url)) ){
            File::checkPermissions($path);
            copy($url, $path.$filename);
            return $filename;
        }

        //load image
        $img = EWideImage::load($webroot.$url);

		//output array width sizes
        $output = self::prepareSizes($img, $params);

		//resize
        $changed = $img->resize($output[0], $output[1]);
    
        //crop
        if(isset($params['crop'])){
        	$changed = $changed->crop('center', 'middle', $params['width'], $params['height']);
        }
        
        //watermark
        if(isset($params['watermark']) && $params['watermark']){
        	self::addWatermark($changed, $params, 100, 'center', 'center');
        }

        //пример blur-а
        // $gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
        // $changed->applyConvolution($gaussian, 16, 0)->saveToFile($blur.$filename);

        
        //сохранения картинки
        if(strpos($url, 'http') == 0) 
            $webroot = Yii::getPathOfAlias('webroot').DS;

        File::checkPermissions($webroot.$path);

        $changed->saveToFile($webroot.$path.$filename);

        //удаление картинок из памяти
        unset($img);
        unset($changed);
        
        if (isset($wimg)) {
            unset($wimg);
        }

        return $filename;
	}

	/**
	 * подготовка (обрезка изображения для дальнейшей обрезки)
	 * @param  EWideImage $image    ссылка на изображение или размеры изображения
	 * @param  array $params        размеры результирующего изображения
	 * @return 
	 */
	public static function prepareSizes(&$image, $params){

		//если передан один параметр
        if(empty($params['width']) && !empty($params['height']) ) {
            return array(null, $params['height']);
        }
        elseif(empty($params['height']) && !empty($params['width']) ){
            return array($params['width'], null);  
        }
        elseif(!empty($params['width']) && !empty($params['height']) ) {
           
            if(isset($params['crop'])){ //обрезка
                if(is_array($image)){
                    $iwidth = $image[0]; 
                    $iheight = $image[1];
                }
                else{
                    $iwidth = $image->getWidth(); 
                    $iheight = $image->getHeight();
                }
                
                $iq = $iheight / $iwidth;

                $owidth = $params['width']; 
                $oheight = $params['height']; 
                $oq = $oheight / $owidth;  

                if($oq < 1){ // широкий выход
                    if($iq < 1){ // широкий оригинал
                        if($iq<$oq){
                            return array(null, $oheight);
                        }
                        else{
                            return array($owidth, null);
                        }
                    }
                    else{ // узкий или квадратный оригинал
                        return array($owidth, null);
                    }
                }

                elseif($oq > 1){ // узкий выход
                    if($iq > 1){ // узкий оригинал
                        if($iq<$oq)
                            return array(null, $oheight);
                        else
                            return array($owidth, null);
                    }
                    else // широкий или квадратный оригинал
                        return array(null, $oheight);
                }
                
                else{ //квадратный выход
                    if($iq<1) // широкий оригинал
                        return array(null, $oheight);
                    else // узкий оригинал
                        return array($owidth, null);
                }

            }
            else
                return array($params['width'], $params['height']); 
        }
	}

    /**
     * Используется для кастомной обрезки уже загруженного изображения
     * @param  array $options   параметры обрезки
     * @params array $params    пнастройки обрезки по умолчанию для файла
     */
    public function crop($options, $params){
        
        //увеличить лимит для загрузки
        self::addLimits(param('upload_max_filesize'));

        $path = $this->path;
        $sizes = explode(',', $this->sizes);

        foreach($sizes as $size){
            //пропустить оригинал или если изображение не было изменено
            if($size == 'original' || $options[$size]['x'] == null) continue;
            
            $source = $path.'original'.DS.$this->filename;

            //проверка на существование файла
            if(!$source || !File::fileExists($source)){
                throw new CHttpException(404, "Файл для загрузки не найден ({$source})");
            }

            //load image
            $img = EWideImage::load($source);
            
            //crop
            $img = $img->crop($options[$size]['x'], $options[$size]['y'], $options[$size]['w'], $options[$size]['h']);

            //crop
            $img = $img->resize($options[$size]['width'], $options[$size]['height']);

            //watermark
            if(isset($params[$size]['watermark']) && $params[$size]['watermark']){
                self::addWatermark($img, $params[$size], 100, 'center', 'center');
            }

            //сохранения картинки
            File::checkPermissions($path.$size.DS);
            $img->saveToFile($path.$size.DS.$this->filename);

            //удаление картинок из памяти
            unset($img);
            
            if (isset($wimg)) {
                unset($wimg);
            }

        }
    }

    /**
     * добавить к изображению watermark
     */
    private static function addWatermark(&$img, $params, $opacity, $_x, $_y, $offsetX = 0, $offsetY = 0){
       
        switch(self::luminance($img)){
            case self::IMAGE_DARKEN:
                $key = 'watermark_image_light';
                break;
            case self::IMAGE_LIGHTEN:
                $key = 'watermark_image_dark';
                break;
            default:
                $key = 'watermark_image';
                break;
        }

        //load watermark
        $watermark = isset($params[$key]) ? 
                            $params[$key] : param($key);

        if( File::fileExists($watermark)){
            $wimg = EWideImage::load($watermark);

            switch($_x){
                case 'left':
                    $x = $offsetX;
                    break;
                case 'right':
                    $x = ($img->getWidth() - $wimg->getWidth()) - $offsetX;
                    break;
                case 'center':
                default: 
                    $x = 'center';
                    break;
            }

            switch($_y){
                case 'top':
                    $y = $offsetY;
                    break;
                case 'bottom':
                    $y = ($img->getHeight() - $wimg->getHeight()) - $offsetY;
                    break;
                case 'center':
                default: 
                    $y = 'middle';
                    break;
            }

            $img = $img->merge($wimg, $x, $y, $opacity);
        }
    }

    /**
     * Поворот изображения
     */
    public function rotate($side, $_size){
        //увеличить лимит для загрузки
        self::addLimits(param('upload_max_filesize'));

        $path = $this->path;
        $sizes = explode(',', $this->sizes);

        foreach($sizes as $size){
            
            $source = $path.$size.DS.$this->filename;

            //проверка на существование файла
            if(!$source || !File::fileExists($source)){
                throw new CHttpException(404, "Файл для загрузки не найден ({$source})");
            }

            //load image
            $img = EWideImage::load($source);
            
            $angle = ($side == 'left'  ? -90 : 90);
            
            $img = $img->rotate($angle);

            //сохранениe картинки
            File::checkPermissions($path.$size.DS);
            $img->saveToFile($path.$size.DS.$this->filename);

            //удаление картинок из памяти
            unset($img);
            
            if (isset($wimg)) {
                unset($wimg);
            }

        }

        return bu().$path.$_size.DS.$this->filename.'?'.time();
    }


	public function afterDelete(){
        //удаление картинки
        parent::afterDelete();

        if($this->sizes){
            $sizes = explode(',', $this->sizes);

            foreach($sizes as $size){
                @unlink($this->path.$size.DS.$this->filename);
            }
        }
	}

    /**
     * Url к изображению, абсолютный или относительный
     */
    public function getImageUrl($size = false, $absoluteUrl = false){
        $path = $this->path;
        if($size){
            $path .= $size;
        }

        $prefix = $absoluteUrl ? request()->hostInfo : '';
        $url = Yii::app()->baseUrl.DS.$path.DS;

        return $prefix.$url.$this->filename;
    }

    /**
     * Путь к изображению
     */
    public function getImagePath($size = false, $withFileName = true, $absolutePath = false){
        $path = $this->path;
        if($size){
            $path .= $size.DS;
        }

        $prefix = $absolutePath ? Yii::getPathOfAlias('webroot') . DS : '';

        return $prefix.$path. ($withFileName ? $this->filename : '');
    }

    /**
     * Рисует thumbnail для изображения
     */
    public function getThumbnail($size = false, 
                                $width = false, 
                                $height = false, 
                                $title = false,
                                $params = array(), 
                                $absoluteUrl = false, 
                                $refresh = false){
        
        //if(!$this->filename || !file_exists($this->path.$size.DS.$this->filename)) return '';

        $params = array_merge($params, array('width'=>($width? $width : 'auto'), 'height'=>($height? $height : 'auto')) );
        $alt = $title ? $title : $this->title;

        return CHtml::image($this->getImageUrl($size, $absoluteUrl). ($refresh ? '?'.time() : ''), $alt, $params);

    }

    public function afterFind(){

        parent::afterFind();
        //для title
        // $this->setAttributes($this->lang->attributes); 
        return true;
    }

    /**
     * Проверяет темное ли изображение или светлое
     */
    private static function luminance(&$img){
    
        //$img = EWideImage::load($this->getImagePath($size));
      
        $luminance = self::getAvgLuminance($img, 10);        
        
        // assume a medium gray is the threshold, #acacac or RGB(172, 172, 172)        
        // this equates to a luminance of 170        
        if ($luminance > 170) return self::IMAGE_LIGHTEN;
        else return self::IMAGE_DARKEN;

    }

    // get average luminance, by sampling $num_samples times in both x,y directions    
    private static function getAvgLuminance(&$img, $num_samples=10) {        
        
        $width = $img->getWidth();        
        $height = $img->getHeight();   

        $x_step = intval($width/$num_samples);        
        $y_step = intval($height/$num_samples);  

        $total_lum = 0;        
        $sample_no = 1;        

        for ($x=0; $x<$width; $x+=$x_step) {            
            for ($y=0; $y<$height; $y+=$y_step) {                
                $rgb = $img->getColorAt($x, $y);                
                $r = ($rgb >> 16) & 0xFF;                
                $g = ($rgb >> 8) & 0xFF;                
                $b = $rgb & 0xFF;                

                // choose a simple luminance formula from here                
                // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color                
                $lum = ($r+$r+$b+$g+$g+$g)/6;                
                $total_lum += $lum;                
                // debugging code     
                // echo "$sample_no - XY: $x,$y = $r, $g, $b = $lum<br />";                
                $sample_no++;            
            }        
        }        

        // work out the average        
        $avg_lum  = $total_lum/$sample_no;        
        return $avg_lum;     
    }

    private static function addLimits($limit){
        ini_set('memory_limit', $limit);
        ini_set('post_max_size', $limit);
        ini_set('upload_max_filesize', $limit);
    }

}


