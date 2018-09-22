<?php

namespace powerkernel\support\traits;

use powerkernel\support\Module;

trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('support');
    }
}
