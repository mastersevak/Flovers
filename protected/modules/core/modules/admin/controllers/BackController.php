<?php
/**
*/
class BackController extends BController
{
    public function actions()
    {
        return CMap::mergeArray(parent::actions(), [
            'fastlinks' => [
                'class' => 'market.widgets.fastLinks.actions.FastLinksAction'
            ]
        ]);
    }
    
    public function actionIndex(){
        $this->pageTitle = t('admin', 'Главная');
        $this->render('index');
    }

    public function actionClearCache(){
        Yii::app()->cache->flush();
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
}
