<?php
namespace powerkernel\support\jobs;

use powerkernel\support\Mailer;
use powerkernel\support\models\Content;
use powerkernel\support\traits\ModuleTrait;
use yii\base\BaseObject;

class SendMailJob extends BaseObject implements \yii\queue\JobInterface
{
    use ModuleTrait;

    public $contentId;

    public function execute($queue)
    {
        $content = Content::findOne(['id' => $this->contentId]);
        if ($content !== null) {
            $email = $content->ticket->user_contact;
            /* send email */
            $subject = \powerkernel\support\Module::t('support', '[{APP} Ticket #{ID}] Re: {TITLE}',
                ['APP' => Yii::$app->name, 'ID' => $content->ticket->hash_id, 'TITLE' => $content->ticket->title]);
            $this->mailer->sendMessage(
                $email,
                $subject,
                'reply-ticket',
                ['title' => $subject, 'model' => $content]
            );
        }
    }

    protected function getMailer()
    {
        return \Yii::$container->get(Mailer::className());
    }
}