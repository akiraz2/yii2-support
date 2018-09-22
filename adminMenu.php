<?php

/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */


use common\Core;
use common\widgets\SideMenu;


$menu = [
    'title' => \powerkernel\support\Module::t('support', 'Ticket System'),
    'icon' => 'support',
    'items' => [
        [
            'icon' => 'ticket',
            'label' => \powerkernel\support\Module::t('support', 'Tickets'),
            'url' => ['/support/ticket/index'],
            'active' => Core::checkMCA('support', 'ticket', '*')
        ],
        [
            'icon' => 'cubes',
            'label' => \powerkernel\support\Module::t('support', 'Categories'),
            'url' => ['/support/cat/index'],
            'active' => Core::checkMCA('support', 'cat', '*')
        ],
    ],
];
$menu['active'] = SideMenu::isActive($menu['items']);
return [$menu];