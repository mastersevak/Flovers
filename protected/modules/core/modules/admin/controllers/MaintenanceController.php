<?php 

/**
 * Maintenance Controller
 * Сайт не доступен, проводятся ремонтные работы
 */
 class MaintenanceController extends BController
 {

 	public function actionIndex(){
        $this->layout = '/layouts/maintenance';

 		$this->render('/errors/maintenance');
 	}

 	/**
     * Обработчик ошибок для backend
     */
    public function actionBackendError()
    {
        $this->layout = '//layouts/error';


        
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest){
                echo $error['message'];
                Yii::app()->end();
            }

            $cs = Yii::app()->clientScript;

            switch($error['code']){
                case '404':
                    $this->pageTitle = 'Страница не найдена';
                    break;
                case '500':
                    $this->pageTitle = 'Ошибка сервера';
                    break;
                default:
                    $this->pageTitle = 'Ошибка';
                    break;

            }

            $this->render('/errors/error', $error);
        }
    }
 	
 } 

 ?>