<?php
/* @var $this yii\web\View */
/* @var $model \powerkernel\support\models\Content */
?>

<?= \powerkernel\support\Module::t('support', 'Ticket #{ID}: New reply from {NAME}:', [
    'ID' => $model->ticket->id,
    'NAME' => !empty($model->user_id) ? $model->user->{\Yii::$app->getModule('support')->userName} : \powerkernel\support\Module::t('support',
        'Ticket System')
]) ?>

<?= Yii::$app->formatter->asNtext($model->content) ?>


<?= \powerkernel\support\Module::t('support', 'View Ticket: {URL}', ['URL' => $model->ticket->getUrl(true)]) ?>