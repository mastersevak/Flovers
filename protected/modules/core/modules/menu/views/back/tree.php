<?php

    $this->widget('core.extensions.jstree.SJsTree', [
        'id' => 'MenuTree',
        'data' => $treeArray,
        'multilang' => true,
        'treeHtmlOptions' => [
            'data-prepare-url' => $this->createUrl('prepareupdate'),
            'data-move-url' => $this->createUrl('move'),
            'data-delete-url' => $this->createUrl('delete')
        ],
        'itemHtmlOptions' => [
            'data-target' => '#items-form-modal',
            'data-model' => 'Menu',
            'data-title' => 'Редактировать меню',
            'data-action' => $this->createUrl('/core/menu/back/updateMenu'),
        ],
        'options' =>[
            'core' => ['initially_open'=>'MenuTreeNode_1'],
            'plugins' => ['themes', 'json_data', 'html_data','ui','dnd','crrm', 'search','cookies', 'contextmenu'],
            'crrm' => [
                'move' => ['check_move'=>'js: function(m){
                    // Disallow categories without parent.
                    // At least each category must have `root` category as parent.
                    var p = this._get_parent(m.r);
                    if (p == -1) return false;
                    return true;
                }'
                ]
            ],
            'cookies' => [
                'save_selected'=>false,
            ],
            'ui' => [
                'initially_select'=>['#MenuTreeNode_'.(int)Yii::app()->request->getParam('id')]
            ],
            'contextmenu' => [
                'select_node' => true,
                'items' => [
                    'create' => [
                        'action' => 'js: function(obj){
                            $.fn.menu("addNode", {
                                "target" : "#items-form-modal",
                                "model" : "Menu",
                                "action" : "'.$this->createUrl("/core/menu/back/create").'",
                                "title" : "Добавить меню",
                                }, obj);
                        }'
                    ],
                    'update' => [
                        'label' => 'Update',
                        'action' => 'js: function(obj){
                            $.fn.menu("updateMenu", obj);
                        }'
                    ],
                    'remove' => [
                        'label' => 'Delete',
                        'action' => 'js: function(obj){
                            $.fn.tree("deleteNode", obj);
                        }'
                    ],
                    'rename' => false,
                    'ccp' => false,
                ]
            ]
        ]
    ]);
?>