<?php

/** @var \Icinga\Application\Modules\Module $this */

$section = $this->menuSection(N_('__Modulename__'), [
    'permission' => '__modulename__',
    'url' => '__modulename__/classic',
    'icon' => 'beaker',
    'priority' => 910
]);


?>
