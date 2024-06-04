<?php

$section->add(N_('Files'))
    ->setUrl('__modulename__/file')
    ->setPermission('__modulename__/file')
    ->setPriority(30);


$this->providePermission(
    '__modulename__/file',
    $this->translate('Allow the user to list files')
);

$this->providePermission(
    '__modulename__/file/upload',
    $this->translate('Allow uploading files')
);

$this->providePermission(
    '__modulename__/file/view',
    $this->translate('Allow viewing files')
);

$this->providePermission(
    '__modulename__/file/download',
    $this->translate('Allow download files')
);

$this->providePermission(
    '__modulename__/file/delete',
    $this->translate('Allow deleting files')
);

?>