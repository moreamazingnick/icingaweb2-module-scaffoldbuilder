<?php

$this->provideConfigTab('config/moduleconfig', array(
    'title' => $this->translate('Module Configuration'),
    'label' => $this->translate('Module Configuration'),
    'url' => 'moduleconfig'
));

$this->providePermission('__modulename__/config/moduleconfig', $this->translate('allow access to __modulename__ configuration'));

?>