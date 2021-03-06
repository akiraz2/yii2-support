<?php

namespace powerkernel\support;

use Yii;
use yii\base\Component;

/**
 * Mailer.
 */
class Mailer extends Component
{
    /** @var string */
    public $viewPath = '@vendor/powerkernel/yii2-support/mail';

    /** @var string|array Default: `Yii::$app->params['adminEmail']` OR `no-reply@example.com` */
    public $sender;

    /** @var string|array Default: `Yii::$app->params['adminEmail']` */
    public $toEmail;

    /** @var \yii\mail\BaseMailer Default: `Yii::$app->mailer` */
    public $mailerComponent;

    /** @var \powerkernel\support\Module */
    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule('support');
        parent::init();
    }

    public function sendMessageToSupportEmail($subject, $view, $params = [])
    {
        if ($this->toEmail === null) {
            $this->toEmail = isset(Yii::$app->params['adminEmail']) ?
                Yii::$app->params['adminEmail']
                : 'no-reply@example.com';
        }

        return $this->sendMessage($this->toEmail, $subject, $view, $params);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $params
     *
     * @return bool
     */
    public function sendMessage($to, $subject, $view, $params = [])
    {
        $mailer = $this->mailerComponent === null ? Yii::$app->mailer : Yii::$app->get($this->mailerComponent);
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;

        if ($this->sender === null) {
            $this->sender = isset(Yii::$app->params['adminEmail']) ?
                Yii::$app->params['adminEmail']
                : 'no-reply@example.com';
        }
        return $mailer->compose(['html' => $view . '-html', 'text' => $view . '-text' ],
            $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
