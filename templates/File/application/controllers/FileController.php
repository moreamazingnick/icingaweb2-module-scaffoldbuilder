<?php

namespace Icinga\Module\__Modulename__\Controllers;


use Icinga\Application\Logger;
use Icinga\Application\Modules\Module;
use Icinga\Exception\Http\HttpException;

use Icinga\Module\__Modulename__\FilesTable;
use Icinga\Module\__Modulename__\Forms\FileUploadForm;

use Icinga\Web\Notification;
use Icinga\Web\Url;

use ipl\Html\Html;
use ipl\Web\Compat\CompatController;
use ipl\Web\Widget\ButtonLink;

class FileController extends CompatController
{
    protected $path = "";
    public function checkAndCreateFolder($folder)
    {
        if (!file_exists($folder)) {
            try {
                mkdir($folder, 0755, true);
            } catch (\Throwable $e) {
                Notification::error($folder . " could not be created, create manually and / or change permissions");
                Logger::error($folder . " could not be created, create manually and / or change permissions");
                return false;
            }

        }

        if (!is_writable($folder)) {
            Logger::error($folder . " is not writeable, please fix manually");
            Notification::error($folder . " is not writeable, please fix manually");
            return false;
        }
        return true;
    }

    public function init()
    {
        $this->path = Module::get('__modulename__')->getConfigDir().DIRECTORY_SEPARATOR."files";
        $this->checkAndCreateFolder($this->path);
    }

    public function deleteAction()
    {
        $this->assertPermission('__modulename__/file/delete');

        $this->addTitleTab($this->translate('Delete'));

        $fileToGet = $this->params->shift('name');
        $filePath = $this->path.DIRECTORY_SEPARATOR.$fileToGet;
        if (strpos(realpath($filePath), $this->path) !== false && file_exists($filePath)) {
            unlink($filePath);

            $this->redirectNow('__modulename__/file');
            return;
        }
        throw new HttpException(401,"Don't do this again...");

    }
    public function uploadAction()
    {
        $this->assertPermission('__modulename__/file/upload');

        $this->addTitleTab($this->translate('Upload'));
        $title = $this->translate('Upload a file');
        $this->view->headline= Html::tag('h1', null, $title);
        $form = (new FileUploadForm())->setUploadPath($this->path);
        $form->handleRequest();

        $this->view->form= $form;
    }

    public function viewAction()
    {
        $this->assertPermission('__modulename__/file/view');

        $this->addTitleTab($this->translate('View'));

        $fileToGet = $this->params->shift('name');
        $filePath = $this->path.DIRECTORY_SEPARATOR.$fileToGet;
        if (strpos(realpath($filePath), $this->path) !== false && file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $pre= Html::tag('pre',null,$fileContent);
            $h1 = Html::tag('h1',null,"File: ".$fileToGet);
            $this->addContent($h1);
            $this->addContent($pre);
            return;
        }
        throw new HttpException(401,"Don't do this again...");

    }


    public function indexAction()
    {
        $this->assertPermission('__modulename__/file');
        $this->addTitleTab($this->translate('Files'));

        if ($this->hasPermission('__modulename__/file/upload')) {
            $this->addControl(
                (new ButtonLink($this->translate('Upload'), \ipl\Web\Url::fromPath('__modulename__/file/upload'), 'plus'))
                    ->openInModal()
            );
        }
        $directory = $this->path;
        $files = $this->fetchFileList();


        $data =[];
        foreach ($files as $file) {
            $item = ['name'=>$file, 'size'=>filesize($directory .DIRECTORY_SEPARATOR.$file)];
            $data[]= (object) $item;
        }

        $this->addContent((new FilesTable())->setData($data));


    }

    public function fetchFileList(){
        $directory = $this->path;
        $files  = scandir($directory);

        $files = array_diff($files, array('.', '..'));

        $files = array_filter($files, function($file) use ($directory) {
            return is_file($directory .DIRECTORY_SEPARATOR.$file);
        });

        return $files;
    }
    public function downloadAction()
    {
        $this->assertPermission('__modulename__/file/download');
        $fileToGet = $this->params->shift('name');
        $filePath = $this->path.DIRECTORY_SEPARATOR.$fileToGet;
        if (strpos(realpath($filePath), $this->path) !== false && file_exists($filePath)) {
            ob_get_clean();
            header('Content-Description: File Transfer');
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . $fileToGet . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        }


    }


    public function checkFolder($folder)
    {
        if (!file_exists($folder)) {
            Notification::error(t($folder . " is not readable, please fix manually"));
            return false;
        }

        return true;
    }



}