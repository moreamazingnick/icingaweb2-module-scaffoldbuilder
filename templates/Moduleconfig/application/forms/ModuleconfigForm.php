<?php

/* Originally from Icinga Web 2 Reporting Module (c) Icinga GmbH | GPLv2+ */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__\Forms;

use Icinga\Forms\ConfigForm;

class ModuleconfigForm extends ConfigForm
{
    protected static $dummyPassword = '_web_form_m0r34m4z1n6n1ck';

    public function init()
    {

        $this->setName('__modulename___settings');
        $this->setSubmitLabel($this->translate('Save Changes'));
    }

    public function createElements(array $formData)
    {


        $this->addElement('text', 'settings_sometext', [
            'label' => $this->translate('some text'),

        ]);

        $this->addElement('password', 'settings_somepassword', [
            'label' => $this->translate('some password'),
            'description' => $this->translate(
                'A meaningful description'
            ),
            'autocomplete' => 'new-password',
            'required' => true,
            'renderPassword' => true
        ]
        );

        $this->addElement('select', 'settings_somedropdown', [
            'label' => $this->translate('some dropdown'),
            'multiOptions' => [
                '0' => "ok",
                '1' => "warning",
                '2' => "critical",
                '3' => "unknown",

            ],
            'description' => $this->translate(
                'A meaningful description'
            ),
            'value' => '2',
            'required' =>  true,
        ]);
        $this->addElement('checkbox','settings_somecheckbox',
            [
                'label' => $this->translate('Some checkbox/slider'),
                'required' => false,
                'description' => $this->translate(
                    'Whether we should enable or disable something'
                ),
                'value' => 0
            ]
        );

    }
    public function onRequest()
    {
        parent::onRequest();
        foreach ($this->getElements() as $element) {
            if ($element->getType() === 'Zend_Form_Element_Password' && $element->getValue() != null && strlen($element->getValue()) > 0) {
                $element->setValue(static::$dummyPassword);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValues($suppressArrayNotation = false)
    {
        $values = parent::getValues($suppressArrayNotation);
        $resource = "settings";
        if ($resource !== null && $this->config->hasSection($resource)) {

            $resourceConfig = $this->config->getSection($resource)->toArray();

            foreach ($this->getElements() as $element) {
                if ($element->getType() === 'Zend_Form_Element_Password') {
                    $name = $element->getName();
                    $name2 = str_replace($resource . "_", "", $name);

                    if (isset($values[$name]) && $values[$name] === static::$dummyPassword) {
                        if (isset($resourceConfig[$name2])) {
                            $values[$name] = $resourceConfig[$name2];
                        } else {
                            unset($values[$name]);
                        }

                    }
                }
            }
        }

        return $values;
    }


}