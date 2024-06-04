<?php

$this->provideConfigTab('config/moduleconfig', array(
    'title' => $this->translate('Module Configuration'),
    'label' => $this->translate('Module Configuration'),
    'url' => 'moduleconfig'
));

$this->providePermission('config/__modulename__', $this->translate('allow access to __modulename__ configuration'));

?>