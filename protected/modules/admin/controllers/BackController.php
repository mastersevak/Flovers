<?php
/**
*/
class BackController extends BController
{

    public function actionIndex(){
        $this->pageTitle = t('admin', 'Главная');
        $this->render('index');
    }

    public function actionCache(){
        $this->pageTitle = t('admin', 'Manage');

        $this->render('cache');
    }

    public function actionClearCache(){
        Yii::app()->cache->flush();
        // apc_clear_cache();
        echo "Кеш очищен";
    }

    public function actionCreateCache(){
        //1. actionLeisurePhotos
        $this->actionLeisurePhotos();
        //2. actionLeisureNames
        $this->actionLeisureNames();
        //3. actionBanners
        $this->actionBanners();
    }

    //сгенерироватъ картинки для заведений
    public function actionLeisurePhotos(){
        $photoalbums = Photoalbum::model()->findAll();

        foreach($photoalbums as &$one){
            if($leisures = $one->leisures){
                $leisures[0]->cacheMainPhoto();
            }

            unset($one);
        }

        echo "Картинки сгенерированы".BR;
    }

    //сгенерировать названия заведений
    public function actionLeisureNames(){
        
        Leisure::cacheLeisureNames();

        echo "Названия заведений сгенерированы".BR;
    }

    //сгенерировать названия заведений
    public function actionBanners(){
        
        Banner::cacheBanners();
        echo "Банеры сгенерированы".BR;
    }
}
