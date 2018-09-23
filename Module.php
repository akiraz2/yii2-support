<?php

namespace powerkernel\support;

use Yii;

/**
 * support module definition class
 */
class Module extends \yii\base\Module
{
    /** @var string DB type `sql` or `mongodb` */
    public $dbType ='sql';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'powerkernel\support\controllers';

    /** @var linked user (for example, 'common\models\User::class' */
    public $userModel;// = \common\models\User::class;

    /** @var string Primary Key for user table, by default 'id' */
    public $userPK = 'id';

    /** @var string username uses in view (may be field `username` or `email` or `login`) */
    public $userName = 'username';

    public $urlViewUser;

    /** @var array Mailer configuration */
    public $mailer = [];

    /**
     * Translate message
     * @param $message
     * @param array $params
     * @param null $language
     * @return mixed
     *
     * public static function t($message, $params = [], $language = null)
     * {
     * return Yii::$app->getModule('support')->translate($message, $params, $language);
     * }*/

    /**
     * Translate message
     * @param $message
     * @param array $params
     * @param null $language
     * @return mixed
     */
    public static function translate($message, $params = [], $language = null)
    {
        return self::t('support', $message, $params, $language);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
     *
     * The translation will be conducted according to the message category and the target language will be used.
     *
     * You can add parameters to a translation message that will be substituted with the corresponding value after
     * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
     *
     * ```php
     * $username = 'Alexander';
     * echo \Yii::t('app', 'Hello, {username}!', ['username' => $username]);
     * ```
     *
     * Further formatting of message parameters is supported using the [PHP intl extensions](http://www.php.net/manual/en/intro.intl.php)
     * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     *
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('powerkernel/' . $category, $message, $params, $language);
    }

    public function isMongoDb()
    {
        return $this->dbType === 'mongodb';
    }
}
