<?php

use powerkernel\support\models\Cat;
use powerkernel\support\models\Ticket;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel powerkernel\support\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


/* breadcrumbs */
$this->params['breadcrumbs'][] = $this->title;

/* misc */
$this->registerJs('$(document).on("pjax:send", function(){ $(".grid-view-overlay").removeClass("hidden");});$(document).on("pjax:complete", function(){ $(".grid-view-overlay").addClass("hidden");})');
//$js=file_get_contents(__DIR__.'/index.min.js');
//$this->registerJs($js);
//$css=file_get_contents(__DIR__.'/index.css');
//$this->registerCss($css);
?>
<div class="ticket-index">
    <div class="box box-primary">
        <div class="box-body">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?php Pjax::begin(); ?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'cat',
                            'value' => function ($model) {
                                return $model->category->title;
                            },
                            'filter' => Cat::getCatList()
                        ],
                        'title',
                        //'created_by',
                        // 'created_at',
                        // 'updated_at',
                        [
                            'attribute' => 'created_by',
                            'value' => function ($model) {
                                $module = Yii::$app->getModule('support');
                                if ($module->urlViewUser) {
                                    return \yii\helpers\Html::a($model->createdBy->{$module->userName},
                                        Yii::$app->urlManager->createUrl([
                                            $module->urlViewUser,
                                            $module->userPK => $model->created_by
                                        ]),
                                        ['data-pjax' => 0]);
                                } else {
                                    return $model->createdBy->{$module->userName};
                                }
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->statusColorText;
                            },
                            'filter' => Ticket::getStatusOption(),
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => 'createdAt',
                            'format' => 'dateTime',
                            'filter' => DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'dateFormat' => 'yyyy-MM-dd',
                                'options' => ['class' => 'form-control']
                            ]),
                            'contentOptions' => ['style' => 'min-width: 80px']
                        ],
                        ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}'],
                    ],
                ]); ?>
            </div>
            <?php Pjax::end(); ?>

        </div>
        <!-- Loading (remove the following to stop the loading)-->
        <div class="overlay grid-view-overlay hidden">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        <!-- end loading -->
    </div>
</div>
