<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__\Controllers;


use Icinga\Exception\Http\HttpException;
use Icinga\Exception\NotFoundError;

use Icinga\Module\__Modulename__\__Modelname__Restrictor;
use Icinga\Module\__Modulename__\Controller;
use Icinga\Module\__Modulename__\Forms\__Modelname__Form;
use Icinga\Module\__Modulename__\Model\__Modelname__;
use Icinga\Web\Notification;
use ipl\Html\Form;
use Icinga\Module\__Modulename__\Common\Database;

use ipl\Stdlib\Filter;
use ipl\Web\Url;


class __Modelname__Controller extends Controller
{
    /** @var __Modelname__ The __Modelname__ object */
    protected $__modelname__;
    protected $db;

    public function init()
    {
        if(!$this->Auth()->hasPermission("__modulename__/__modelname__/modify")){
            throw new HttpException(401,"Not allowed!");
        }
        $this->db=Database::get();

    }

    public function newAction()
    {

        $this->setTitle($this->translate('New __Modelname__'));

        $values = [];

        $form = (__Modelname__Form::fromId(null))->setDb($this->db)
            ->setAction((string) Url::fromRequest())->setRenderCreateAndShowButton(false)
            ->populate($values)
            ->on(__Modelname__Form::ON_SUCCESS, function (__Modelname__Form $form) {
                $pressedButton = $form->getPressedSubmitElement();
                if ($pressedButton && $pressedButton->getName() === 'remove') {
                    Notification::success($this->translate('Removed __Modelname__ successfully'));


                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('__modulename__/__modelnamePL__')
                    );
                } else {
                    Notification::success($this->translate('Updated __Modelname__ successfully'));

                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('__modulename__/__modelnamePL__')
                    );
                }
            })
            ->handleRequest($this->getServerRequest());

        $this->addContent($form);

    }

    public function editAction()
    {

        $this->setTitle($this->translate('Edit __Modelname__'));

        $id = $this->params->getRequired('id');

        $query = __Modelname__::on($this->db)->with([

        ]);
        $query->filter(Filter::equal('id', $id));

        $restrictor = new __Modelname__Restrictor();
        $restrictor->applyRestrictions($query);

        $__modelname__ = $query->first();
        if ($__modelname__ === null) {
            throw new NotFoundError(t('Entry not found'));
        }

        $this->__modelname__ = $__modelname__;




        $values = $this->__modelname__->getValues();




        $form = (__Modelname__Form::fromId($id))->setDb($this->db)
            ->setAction((string) Url::fromRequest())->setRenderCreateAndShowButton(false)
            ->populate($values)
            ->on(__Modelname__Form::ON_SUCCESS, function (__Modelname__Form $form) {
                $pressedButton = $form->getPressedSubmitElement();
                if ($pressedButton && $pressedButton->getName() === 'remove') {
                    Notification::success($this->translate('Removed __Modelname__ successfully'));


                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('__modulename__/__modelnamePL__')
                    );
                } else {
                    Notification::success($this->translate('Updated __Modelname__ successfully'));

                    $this->closeModalAndRefreshRemainingViews(
                        Url::fromPath('__modulename__/__modelnamePL__')
                    );
                }
            })
            ->handleRequest($this->getServerRequest());

        $this->addContent($form);

    }

    protected function redirectForm(Form $form, $url)
    {
        if (
            $form->hasBeenSubmitted()
            && ((isset($form->valid) && $form->valid === true)
                || $form->isValid())
        ) {
            $this->redirectNow($url);
        }
    }
}
