<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__\Controllers;

use HttpException;
use Icinga\Application\Config;
use Icinga\Module\__Modulename__\Forms\BackendConfigForm;
use ipl\Web\Compat\CompatController;


class ConfigController extends CompatController
{
    protected $path;
    public function init()
    {
        $this->assertPermission('config/__modulename__');
        parent::init();
    }

    public function backendAction()
    {
        $form = (new BackendConfigForm())
            ->setIniConfig(Config::module('__modulename__'));

        $form->handleRequest();

        $this->view->tabs = $this->Module()->getConfigTabs()->activate('backend');
        $this->view->form = $form;
    }
}
