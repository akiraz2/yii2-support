<?php

namespace powerkernel\support\controllers;

use powerkernel\support\components\BackendFilter;
use powerkernel\support\traits\ModuleTrait;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `ticket` module
 */
class DefaultController extends Controller
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'backend' => [
                'class' => BackendFilter::className(),
                'actions' => [
                    'index',
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return $this->getModule()->adminMatchCallback;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
