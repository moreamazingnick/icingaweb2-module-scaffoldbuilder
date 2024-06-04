<?php

/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__\Controllers;

use ipl\Web\Url;
use ipl\Html\Html;
use Icinga\Web\Controller;

class ClassicController extends Controller
{
    public function indexAction()
    {
        $this->view->tabs = $this->getTabs()
            ->add('__Modulename__ Classic', [
                'label' => $this->translate('__Modulename__ Classic'),
                'url'   => '__modulename__/classic'
            ])->activate('__Modulename__ Classic');
        $headline = Html::tag('h1', 'Hello there!');
        $file = __FILE__;
        $file2= str_replace("controllers/ClassicController.php","views/scripts/classic/index.phtml",$file);
        $text = Html::tag('p', "This is your Controller located at {$file}");
        $text .= Html::tag('p', "The used view file is located at {$file2}");

        $this->view->content = $headline.$text;
    }



}
