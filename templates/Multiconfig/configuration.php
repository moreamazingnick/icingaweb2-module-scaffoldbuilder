<?php

$this->providePermission('__modulename__/__configname__', $this->translate('allow access to __configname__'));

$section->add(N_('__Configname__'))
    ->setUrl('__modulename__/__configname__')
    ->setPermission('__modulename__/__configname__')
    ->setPriority(30);

$this->provideConfigTab('config/__configname__', array(
    'title' => $this->translate('__Configname__'),
    'label' => $this->translate('__Configname__'),
    'url' => '__configname__'
));

?>
