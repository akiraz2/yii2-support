<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \powerkernel\support\models\Ticket */
?>

<?= \powerkernel\support\Module::t('support', 'Hello Admin,') ?>

<?= \powerkernel\support\Module::t('support', '{USER} ({EMAIL}) have opened a ticket with the following message:', [
    'USER' => Html::encode($model->user->{\Yii::$app->getModule('support')->userName}),
    'EMAIL' => Html::encode($model->user->{\Yii::$app->getModule('support')->userEmail})
]) ?>


<?= $model->title ?>

<?= Yii::$app->formatter->asNtext($model->content) ?>



<?= \powerkernel\support\Module::t('support', 'View Ticket: {URL}', ['URL' => $model->getUrl(true)]) ?>