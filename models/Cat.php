<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

namespace powerkernel\support\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%ticket_cat}}".
 *
 * @property integer|\MongoDB\BSON\ObjectID|string $id
 * @property string $title
 * @property integer $status
 * @property integer|\MongoDB\BSON\UTCDateTime $created_at
 * @property integer|\MongoDB\BSON\UTCDateTime $updated_at
 *
 * @property Ticket[] $tickets
 */
class Cat extends CatBase
{


    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;


    /**
     * get status list
     * @param null $e
     * @return array
     */
    public static function getStatusOption($e = null)
    {
        $option = [
            self::STATUS_ACTIVE => Yii::$app->getModule('support')->t('Active'),
            self::STATUS_INACTIVE => Yii::$app->getModule('support')->t('Inactive'),
        ];
        if (is_array($e))
            foreach ($e as $i)
                unset($option[$i]);
        return $option;
    }

    /**
     * color status text
     * @return mixed|string
     */
    public function getStatusColorText()
    {
        $status = $this->status;
        if ($status == self::STATUS_ACTIVE) {
            return '<span class="label label-success">' . $this->statusText . '</span>';
        }
        if ($status == self::STATUS_INACTIVE) {
            return '<span class="label label-default">' . $this->statusText . '</span>';
        }
        return $this->statusText;
    }

    /**
     * get status text
     * @return string
     */
    public function getStatusText()
    {
        $status = $this->status;
        $list = self::getStatusOption();
        if (!empty($status) && in_array($status, array_keys($list))) {
            return $list[$status];
        }
        return Yii::$app->getModule('support')->t('Unknown');
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'status'], 'required'],
            [['status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->getModule('support')->t('ID'),
            'title' => Yii::$app->getModule('support')->t('Title'),
            'status' => Yii::$app->getModule('support')->t('Status'),
            'created_at' => Yii::$app->getModule('support')->t('Created At'),
            'updated_at' => Yii::$app->getModule('support')->t('Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['cat' => 'id']);
    }

    /**
     * get categories list
     * @return array
     */
    public static function getCatList()
    {
        $cats = self::find()->where(['status' => self::STATUS_ACTIVE])->all();
        return ArrayHelper::map($cats, function ($model) {
            return is_a($model, '\yii\mongodb\ActiveRecord') ? (string)$model->_id : $model->id;
        }, 'title');
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->status = (int)$this->status;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
