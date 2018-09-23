<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

namespace powerkernel\support\controllers;

use powerkernel\support\components\BackendFilter;
use powerkernel\support\models\Content;
use powerkernel\support\models\Ticket;
use powerkernel\support\models\TicketSearch;
use powerkernel\support\traits\ModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'backend' => [
                'class' => BackendFilter::className(),
                'actions' => [
                    'manage',
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
                    [
                        'actions' => ['index', 'create', 'view', 'close'],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->title = \powerkernel\support\Module::t('support', 'My Tickets');
        $searchModel = new TicketSearch(['userSearch' => true]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all user Ticket models.
     * @return mixed
     */
    public function actionManage()
    {
        $this->view->title = \powerkernel\support\Module::t('support', 'Tickets');
        $searchModel = new TicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!($model->user_id == Yii::$app->user->id) && !$this->getModule()->adminMatchCallback) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $this->view->title = $model->title;

        // reply
        $reply = new Content();
        $reply->id_ticket = $model->id;
        $reply->user_id = Yii::$app->user->id;
        if ($reply->load(Yii::$app->request->post()) && $reply->save()) {
            if (Yii::$app->user->id == $model->user_id) {
                $model->status = Ticket::STATUS_OPEN;
                $model->save();
            } else {
                $model->status = Ticket::STATUS_WAITING;
                $model->save();
            }
            return $this->redirect([
                'view',
                'id' => is_a($model, '\yii\mongodb\ActiveRecord') ? (string)$model->_id : $model->id
            ]);
        }


        return $this->render('view', [
            'model' => $model,
            'reply' => $reply
        ]);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->view->title = \powerkernel\support\Module::t('support', 'Open Ticket');
        $model = new Ticket();
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'id' => is_a($model, '\yii\mongodb\ActiveRecord') ? (string)$model->_id : $model->id
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * close a ticket
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionClose($id)
    {
        $model = $this->findModel($id);
        if (!($model->user_id == Yii::$app->user->id) && !$this->getModule()->adminMatchCallback) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        if ($model->status != Ticket::STATUS_CLOSED) {
            $post = new Content();
            $post->id_ticket = $model->id;
            $post->user_id = Yii::$app->user->id;
            $module = $this->getModule();
            $userModel = $module->userModel::findOne([$module->userPK => Yii::$app->user->id]);
            $post->content = \powerkernel\support\Module::t('support', '{USER} closed the ticket.',
                ['USER' => $userModel->{$module->userName}]);
            if ($post->save()) {
                $model->status = Ticket::STATUS_CLOSED;
                if (!$model->save()) {
                    Yii::$app->session->setFlash('danger', json_encode($model->errors));
                }
            }
        }

        return $this->redirect([
            'view',
            'id' => is_a($model, '\yii\mongodb\ActiveRecord') ? (string)$model->_id : $model->id
        ]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
