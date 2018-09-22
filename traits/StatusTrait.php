<?php

namespace powerkernel\support\traits;

use powerkernel\support\Module;

trait StatusTrait
{
    public function getStatusList2()
    {
        return self::getStatusList();
    }

    public static function getStatusList()
    {
        return [
            IActiveStatus::STATUS_INACTIVE => Module::t('support', 'STATUS_INACTIVE'),
            IActiveStatus::STATUS_ACTIVE => Module::t('support', 'STATUS_ACTIVE'),
            IActiveStatus::STATUS_ARCHIVE => Module::t('support', 'STATUS_DELETED')
        ];
    }

    public function getStatus($nullLabel = '')
    {
        $statuses = static::getStatusList();
        return (isset($this->status) && isset($statuses[$this->status])) ? $statuses[$this->status] : $nullLabel;
    }
}

interface IActiveStatus
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ARCHIVE = -1;
}
