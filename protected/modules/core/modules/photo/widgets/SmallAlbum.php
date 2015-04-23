<?php 

class SmallAlbum extends SWidget
{

	public $id;
	public $limit = 10;

    public function init(){
        parent::init();

        cs()->registerCssFile($this->assetsUrl.'/css/smallalbum.css');
    }

    public function run() {

        $photoalbum = Photoalbum::model()->findByPk($this->id);

        if($photoalbum){
            $photos = $photoalbum->photos(array('limit' => $this->limit, 'order'=>'RAND()'));
            
            $this->render('smallAlbum', compact('photoalbum', 'photos'));
        }
        
    		
    }
}