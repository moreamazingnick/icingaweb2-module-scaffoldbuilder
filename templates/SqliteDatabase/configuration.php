<?php

$this->providePermission('__modulename__/sqlite-database', $this->translate('allow access to sqlite database management'));

$section->add(N_('Sqlite-Database'))
    ->setUrl('__modulename__/sqlite-database')
    ->setPermission('__modulename__/sqlite-database')
    ->setPriority(30);

?>
