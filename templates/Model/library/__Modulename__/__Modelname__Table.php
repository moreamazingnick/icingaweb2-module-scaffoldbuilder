<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\__Modulename__;

use Icinga\Authentication\Auth;
use Icinga\Module\__Modulename__\Model\__Modelname__;
use Icinga\Web\Url;
use ipl\Orm\Model;

/**
 * Table widget to display a list of __ModelnamePL__
 */
class __Modelname__Table extends DataTable
{
    protected $defaultAttributes = [
        'class'            => 'usage-table common-table table-row-selectable',
        'data-base-target' => '_next'
    ];

    public function createColumns()
    {
        $columns = [];
        foreach ((new __Modelname__())->getColumnDefinitions() as $column=>$options){

            if(is_array($options)) {
                $fieldtype = $options['fieldtype'] ?? "text";

                unset($options['fieldtype']);

                if ($fieldtype === "autocomplete" || $fieldtype === "text" || $fieldtype === "select") {
                    $columns[$column] = $options['label']??$column;

                }elseif ($fieldtype === "checkbox"){
                    $columns[$column."_text"] = [
                        'label'  => $options['label']??$column,
                        'column' => function ($data) use ($column) {
                            return $data->{$column}?t("Yes"):t("No");
                        }
                    ];
                }elseif ($fieldtype === "localDateTime" ){
                    $columns[$column."_txt"] = [
                        'label'  => $options['label']??$column,
                        'column' => function ($data) use ($column) {
                            if($data->{$column}!=null){
                                return $data->{$column}->format("c");
                            }
                            return t("not set");
                        }
                    ];
                }
            }else{
                $columns[$column] = $options;
            }
        }


        return $columns;
    }


    protected function renderRow(Model $row)
    {
        $tr = parent::renderRow($row);

        if (Auth::getInstance()->hasPermission('__modulename__/__modelname__/modify')) {
            $url = Url::fromPath('__modulename__/__modelname__/edit', ['id' => $row->id]);

            $tr->getFirst("td")->getAttributes()->add(['href' => $url->getAbsoluteUrl(), 'data-icinga-modal' => true,
                'data-no-icinga-ajax' => true]);

        }

        return $tr;
    }
}
