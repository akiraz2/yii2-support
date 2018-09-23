<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

namespace powerkernel\support\models;

use powerkernel\support\Mailer;
use powerkernel\support\traits\ModuleTrait;
use Yii;

/**
 * This is the model class for Ticket.
 *
 * @property integer|\MongoDB\BSON\ObjectID|string $id
 * @property integer|\MongoDB\BSON\ObjectID|string $cat
 * @property string $title
 * @property integer $status
 * @property integer|\MongoDB\BSON\ObjectID|string $created_by
 * @property integer|\MongoDB\BSON\UTCDateTime $created_at
 * @property integer|\MongoDB\BSON\UTCDateTime $updated_at
 *
 * @property Content[] $contents
 * @property Category $category
 * @property Account $createdBy
 */
class Ticket extends TicketBase
{
    use ModuleTrait;

    const STATUS_OPEN = 0;
    const STATUS_WAITING = 10;
    const STATUS_CLOSED = 100;

    public $content;

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
        return \powerkernel\support\Module::t('support', 'Unknown');
    }

    /**
     * get status list
     * @param null $e
     * @return array
     */
    public static function getStatusOption($e = null)
    {
        $option = [
            self::STATUS_OPEN => \powerkernel\support\Module::t('support', 'Open'),
            self::STATUS_WAITING => \powerkernel\support\Module::t('support', 'Waiting'),
            self::STATUS_CLOSED => \powerkernel\support\Module::t('support', 'Closed'),
        ];
        if (is_array($e)) {
            foreach ($e as $i) {
                unset($option[$i]);
            }
        }
        return $option;
    }

    /**
     * get status text
     * @return string
     */
    public function getStatusColorText()
    {
        $status = $this->status;
        $list = self::getStatusOption();

        switch ($status) {
            case self::STATUS_CLOSED: $color = 'danger'; break;
            case self::STATUS_OPEN: $color = 'primary'; break;
            case self::STATUS_WAITING: $color = 'warning'; break;
            default: $color = 'default';
        }

        if (!is_null($status) && in_array($status, array_keys($list))) {
            return '<span class="label label-' . $color . '">' . $list[$status] . '</span>';
        }

        return '<span class="label label-' . $color . '">' . \powerkernel\support\Module::t('support',
                'Unknown') . '</span>';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_OPEN],

            [['title', 'cat',], 'required'],
            [['title'], 'string', 'max' => 255],

            [['status'], 'number'],

            [
                ['cat'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::className(),
                'targetAttribute' => ['cat' => Yii::$app->getModule('support')->isMongoDb() ? '_id' : 'id']
            ],
            [
                ['created_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => $this->getModule()->userModel,
                'targetAttribute' => ['created_by' => $this->getModule()->userPK]
            ],

            /* custom */
            [['content'], 'required', 'on' => ['create']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \powerkernel\support\Module::t('support', 'ID'),
            'cat' => \powerkernel\support\Module::t('support', 'Category'),
            'title' => \powerkernel\support\Module::t('support', 'Title'),
            'content' => \powerkernel\support\Module::t('support', 'Content'),
            'status' => \powerkernel\support\Module::t('support', 'Status'),
            'created_by' => \powerkernel\support\Module::t('support', 'Created By'),
            'created_at' => \powerkernel\support\Module::t('support', 'Created At'),
            'updated_at' => \powerkernel\support\Module::t('support', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getContents()
    {
        if (is_a($this, '\yii\mongodb\ActiveRecord')) {
            return $this->hasMany(Content::className(), ['id_ticket' => '_id']);
        } else {
            return $this->hasMany(Content::className(), ['id_ticket' => 'id']);
        }
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getCategory()
    {
        if (is_a($this, '\yii\mongodb\ActiveRecord')) {
            return $this->hasOne(Category::className(), ['_id' => 'cat']);
        } else {
            return $this->hasOne(Category::className(), ['id' => 'cat']);
        }
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getCreatedBy()
    {
        return $this->hasOne($this->getModule()->userModel, [$this->getModule()->userPK => 'created_by']);
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->id;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $ticketContent = new Content();
            $ticketContent->id_ticket = $this->id;
            $ticketContent->content = $this->content;
            $ticketContent->created_by = Yii::$app->user->id;
            if ($ticketContent->save()) {
                if ($this->getModule()->notifyByEmail) {
                    /* send email */
                    $subject = \powerkernel\support\Module::t('support', 'You\'ve received a ticket');
                    $this->mailer->sendMessageToSupportEmail(
                        $subject,
                        [
                            'html' => 'new-ticket-html',
                            'text' => 'new-ticket-text'
                        ],
                        ['title' => $subject, 'model' => $this]
                    );
                }
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * get ticket url
     * @param bool $absolute
     */
    public function getUrl($absolute = false)
    {
        $act = 'createUrl';
        if ($absolute) {
            $act = 'createAbsoluteUrl';
        }
        return \Yii::$app->get($this->getModule()->urlManagerFrontend)->$act([
            'support/ticket/view',
            'id' => (string)$this->id
        ]);
    }

    /**
     * system closes ticket
     */
    public function close()
    {
        if ($this->status != Ticket::STATUS_CLOSED) {
            $post = new Content();
            $post->id_ticket = $this->id;
            $post->created_by = null;
            $post->content = \powerkernel\support\Module::t('support',
                'Ticket was closed automatically due to inactivity.');
            if ($post->save()) {
                $this->status = Ticket::STATUS_CLOSED;
                $this->save();
            }
        }
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function beforeDelete()
    {
        foreach ($this->contents as $content) {
            $content->delete();
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    protected function getMailer()
    {
        return \Yii::$container->get(Mailer::className());
    }
}
