<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

use powerkernel\support\models\Category;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model powerkernel\support\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(Category::getStatusOption()) ?>

    <div class="form-group">
        <?= \yii\helpers\Html::submitButton($model->isNewRecord ? \powerkernel\support\Module::t('support', 'Create') :
            \powerkernel\support\Module::t('support', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
