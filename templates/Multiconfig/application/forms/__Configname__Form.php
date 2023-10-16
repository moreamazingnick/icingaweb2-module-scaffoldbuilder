<?php

/* Originally from Icinga Web 2 Elasticsearch Module (c) 2017 Icinga Development Team | GPLv2+ */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__\Forms;

use Icinga\Data\Filter\Filter;
use Icinga\Forms\RepositoryForm;
use Icinga\Module\__Modulename__\__Configname__IniRepository;

/**
 * Create, update and delete a Config
 */
class __Configname__Form extends RepositoryForm
{
    protected $protectedFields = ['password'];

    public function init()
    {
        $this->repository = new __Configname__IniRepository();
        $this->redirectUrl = '__modulename__/__configname__';
        $this->repository->setProtectedFields($this->protectedFields);

    }
    /**
     * Prepare the form for the requested mode
     * Clear out protectedFields
     */
    public function fetchEntry()
    {
        $entry = parent::fetchEntry();
        if($entry != null ){
            foreach($this->protectedFields as $field){

                if( in_array($field, array_keys(get_object_vars($entry))) ){
                    $entry->{$field} =  "";
                }
            }
        }

        return $entry;
    }


    /**
     * Set the identifier
     *
     * @param   string  $identifier
     *
     * @return  $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set the mode of the form
     *
     * @param   int $mode
     *
     * @return  $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    protected function onUpdateSuccess()
    {
        if ($this->getElement('btn_remove')->isChecked()) {
            $this->setRedirectUrl("__modulename__/__configname__/delete?id={$this->getIdentifier()}");
            $success = true;
        } else {
            $success = parent::onUpdateSuccess();
        }

        return $success;
    }

    protected function createBaseElements(array $formData)
    {
        $this->addElement(
            'text',
            'name',
            array(
                'description'   => $this->translate('Name of the something'),
                'label'         => $this->translate('Config Name'),
                'placeholder'   => 'Your Placeholder',
                'required'      => true
            )
        );

      
        $this->addElement(
            'password',
            'password',
            array(
                'description'       => $this->translate('Some Password field'),
                'label'             => $this->translate('Password'),
                'renderPassword'    => true,
                'required'      => ( $this->mode === 0 )
            )
        );

        $this->addElement(
            'checkbox',
            'nameofcheckbox',
            array(
                'description'       => $this->translate('Some checkbox'),
                'label'             => $this->translate('Checkbox'),
            )
        );

    }

    protected function createInsertElements(array $formData)
    {
        $this->createBaseElements($formData);

        $this->setTitle($this->translate('Create a New __Configname__'));

        $this->setSubmitLabel($this->translate('Save'));
    }

    protected function createUpdateElements(array $formData)
    {
        $this->createBaseElements($formData);

        $this->setTitle(sprintf($this->translate('Update __Configname__ %s'), $this->getIdentifier()));

        $this->addElement(
            'submit',
            'btn_submit',
            [
                'decorators'            => ['ViewHelper'],
                'ignore'                => true,
                'label'                 => $this->translate('Save')
            ]
        );

        $this->addElement(
            'submit',
            'btn_remove',
            [
                'decorators'            => ['ViewHelper'],
                'ignore'                => true,
                'label'                 => $this->translate('Remove')
            ]
        );

        $this->addDisplayGroup(
            ['btn_submit', 'btn_remove'],
            'form-controls',
            [
                'decorators' => [
                    'FormElements',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'control-group form-controls']]
                ]
            ]
        );
    }

    protected function createDeleteElements(array $formData)
    {
        $this->setTitle(sprintf($this->translate('Remove __Configname__ %s'), $this->getIdentifier()));

        $this->setSubmitLabel($this->translate('Yes'));
    }

    protected function createFilter()
    {
        return Filter::where($this->repository->getIdentifierName(), $this->getIdentifier());
    }

    protected function getInsertMessage($success)
    {
        return $success
            ? $this->translate('__Configname__ created')
            : $this->translate('Failed to create __Configname__');
    }

    protected function getUpdateMessage($success)
    {
        return $success
            ? $this->translate('__Configname__ updated')
            : $this->translate('Failed to update __Configname__');
    }

    protected function getDeleteMessage($success)
    {
        return $success
            ? $this->translate('__Configname__ removed')
            : $this->translate('Failed to remove __Configname__');
    }
}
