<? 
$this->renderPartial('_registration', ['model' => new User('registration')]);

$this->beginWidget('UITabs', [
    'paramName' => 'type',
    'tabs' => $this->tabs,
    'ajax' => false
]); ?>

    <div class="tab-content">
        <div class="tab-pane active">
            
            <div class="buttons mb20">
                <? $buttons = []; 

                //добавить пользователя
                if (Yii::app()->request->getParam('type') != 'blocked')
                    $buttons['add'] = ['data-toggle'=>"domodal", 'data-target'=>"#register-modal"];
                
                $buttons = array_merge($buttons, ['DeleteSelected', 'ShowImages']); 

                $this->widget('UIButtons', ['buttons' => $buttons, 'size'=>'small']); ?>
            </div>    

            <? 
            $columns = array(
                [
                    'name' => 'avatar',
                    'header' => '',
                    'type' => 'raw',
                    'filter' => false,
                    'visible'=> $provider->model->isShowThumbnail,
                    'headerHtmlOptions' => ['width'=>40],
                    'htmlOptions' => ['align'=>'center'],
                    'value' => function($data){
                        return CHtml::link($data->getThumbnail('thumb', 35, 35, $data->fullname), $data->backUrl, 
                            ['target' => '_blank', 'rel'=>'tooltip', 'title'=>'Открыть карту']);
                    }
                ],
                [
                    'name' => 'id',
                    'type' => 'raw',
                    'headerHtmlOptions' => ['width'=>50],
                    'htmlOptions' => ['align'=>'center'],
                    'value' => function($data){
                        return CHtml::link($data->id, $data->backUrl, 
                            ['target' => '_blank',  'rel'=>'tooltip', 'title'=>'Открыть карту']);
                    }
                ],
                [
                    'name'  => 'fullname',
                    'type' => 'html',
                    'value' => '$data->getFullName(true)',
                ],
                [
                    'name'  => 'username',
                    'type' => 'html',
                    'value' => '$data->username',
                ],
                [
                    'name'=>'email',
                    'type'=>'raw',
                    'value' => function($data){
                        return CHtml::mailto($data->email, $data->email);
                    }
                ],
                [
                    'class' => 'SButtonColumn',
                    'template' => '{loginas}',
                    'buttons' => [
                        'loginas' => [
                            'label' => '&nbsp;',
                            'options' => [
                                'class' => 'fa fa-sign-in fsize18',
                                'title' => 'Войти от имени пользователя',
                                'rel' => 'tooltip'
                            ],
                            'url' => function($data){
                                return app()->controller->createUrl("loginas", ["id" => $data->id]);
                            },
                            'visible' => function($row, $data) {
                                return $data->isRole(app()->getModule("core")->getModule("rights")->superuserName) ||
                                        Yii::app()->getAuthManager()->checkAccess("Core.Admin.Back.Index", $data->id);
                            },
                            'click' => 'function(event){
                                event.preventDefault();
                                jPost($(this).attr("href"), {}, function($data){if($data.success) location.reload()}, "json");
                            }',
                        ]
                    ],
                    'headerHtmlOptions' => ['width'=>30],
                    'visible' => user()->getState('username') == 'amanukian' || user()->getState('username') == 'alikmanukian',
                ],
                [
                    'class' => 'StatusButtonColumn', 
                    'name' => 'status',
                    'action' => 'status', //<--action for this button
                    'headerHtmlOptions' => ['width'=>70],
                    'header' => 'Активность',
                    'filter' => Lookup::items('StandartStatus'),
                    'value' => '$data->status',
                ]
            );

            if($type == 'blocked')
                $columns[] = [   
                    'class' => 'SButtonColumn',
                    'buttons' => [
                        'update' => ['visible' => 'false'],
                        'delete' => ['url' => 'url("/user/back/delete/", ["id"=>$data->id, "type"=>"'.$type.'"])'],
                        'visible' => $type == 'block'
                    ],
                    'visible' => user()->id == 9502 || user()->id == 2
                ];

            $this->widget('SGridView', array(
                'id'=>'users-table',
                'dataProvider'=>$provider,
                'filter'=>$provider->model,
                'flexible'=>true,
                'showNumColumn' => false,
                'columns'=>$columns,
                'style' => 'blue',
                'type' => 'striped bordered',
                'showButtonsColumn' => $type == 'block'
                
            )); ?>
        </div>
    </div>
<? $this->endWidget(); ?>