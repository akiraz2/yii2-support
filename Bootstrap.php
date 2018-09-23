<?php

namespace powerkernel\support;

use powerkernel\support\traits\ModuleTrait;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

/**
 * support module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('support') && ($module = $app->getModule('support')) instanceof Module) {
            \Yii::$container->set('powerkernel\support\Mailer', $module->mailer);
        }

        // Add module I18N category.
        if (!isset($app->i18n->translations['powerkernel/support'])) {
            $app->i18n->translations['powerkernel/support'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'powerkernel/support' => 'support.php',
                ]
            ];
        }
    }
}
