<?php

/* Originally from Icinga Web 2 Reporting Module (c) Icinga GmbH | GPLv2+ */
/* icingaweb2-module-scaffoldbuilder 2023 | GPLv2+ */

namespace Icinga\Module\__Modulename__\Controllers;


use Icinga\Application\Config;
use Icinga\Web\Controller;
use Icinga\Module\__Modulename__\Forms\ModuleconfigForm;

class ModuleconfigController extends Controller
{

    /**
     * In case you want to use module config settings in this or any other controller this code is for you
     */
    private $settings_sometext;
    private $settings_somepassword;
    private $settings_somedropdown;
    private $settings_somecheckbox;

    public function init()
    {
        $this->assertPermission('config/modules');


        $this->settings_sometext = Config::module('__modulename__', "config")->get('settings', 'sometext') != null ?
            trim(Config::module('__modulename__', "config")->get('settings', 'sometext'), "") : null;

        $this->settings_somepassword = Config::module('__modulename__', "config")->get('settings', 'somepassword') != null ?
            trim(Config::module('__modulename__', "config")->get('settings', 'somepassword'), "") : null;

        $this->settings_somedropdown = Config::module('__modulename__', "config")->get('settings', 'somedropdown') != null ?
            trim(Config::module('__modulename__', "config")->get('settings', 'somedropdown'), "") : null;

        $this->settings_somecheckbox = Config::module('__modulename__', "config")->get('settings', 'somecheckbox') != null ?
            trim(Config::module('__modulename__', "config")->get('settings', 'somecheckbox'), "") : null;

        parent::init();
    }


    public function indexAction()
    {
        $this->assertPermission('__modulename__/config');


        $form = (new ModuleconfigForm())
            ->setIniConfig(Config::module('__modulename__', "config"));

        $form->handleRequest();

        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config/moduleconfig');
        $this->view->form = $form;
    }


    public function createTabs()
    {
        $tabs = $this->getTabs();

        $tabs->add('__modulename__/config', [
            'label' => $this->translate('Configure __Modulename__'),
            'url' => '__modulename__/config'
        ]);

        return $tabs;

    }

}