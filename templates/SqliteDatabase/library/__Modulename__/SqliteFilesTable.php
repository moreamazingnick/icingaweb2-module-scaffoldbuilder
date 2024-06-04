<?php

// originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2

namespace Icinga\Module\__Modulename__;

use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;
use ipl\Html\HtmlString;
use ipl\Web\Url;
use ipl\Web\Widget\Icon;

class SqliteFilesTable extends BaseHtmlElement
{
    protected $tag = 'table';
    protected $defaultAttributes = [
        'class' => 'common-table',
        'data-base-target' => '_next'
    ];
    /**
     * Columns of the table
     *
     * @var array
     */
    protected $columns;

    /**
     * The data to display
     *
     * @var array|\Traversable
     */
    protected $data = [];

    /**
     * Get data to display
     *
     * @return  array|\Traversable
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data to display
     *
     * @param   array|\Traversable  $data
     *
     * @return  $this
     */
    public function setData($data)
    {
        if (! is_array($data)) {
            throw new \InvalidArgumentException('Data must be an array or an instance of Traversable');
        }

        $this->data = $data;

        return $this;
    }

    protected function createColumns()
    {
        return [
            'name' => mt('__modulename__', 'Name'),
            'size' => mt('__modulename__', 'Size'),
            'action' => [
                'label' => mt('__modulename__', 'Action'),
                'attributes' => ['class' => 'icon-col'],
                'column' => function ($data) {
                    return $data;
                },
                'renderer' => function ($data) {
                    $div=Html::tag("div",['style'=>'white-space: nowrap;']);

                    $icon=  new Icon('eye', ['title' => mt('__modulename__', 'View')]);
                    $a = Html::tag("a",['target'=>'_next', 'style'=>'padding-right:1em; display:inline;','href'=>Url::fromPath('__modulename__/sqlite-database/view',['name'=>$data->name])]);
                    $a->add($icon);
                    $div->add($a);

                    $icon=  new Icon('download', ['title' => mt('__modulename__', 'Migrate')]);
                    $a = Html::tag("a",['target'=>'_self', 'style'=>'padding-right:1em; display:inline;','href'=>Url::fromPath('__modulename__/sqlite-database/migrate',['name'=>$data->name])]);
                    $a->add($icon);
                    $div->add($a);
                    return $div;
                }
            ],

        ];
    }

    public function renderHeader()
    {
        $cells = [];

        foreach ($this->columns as $column) {
            if (is_array($column)) {
                if (isset($column['label'])) {
                    $label = $column['label'];
                } else {
                    $label = new HtmlString('&nbsp;');
                }
            } else {
                $label = $column;
            }

            $cells[] = Html::tag('th', $label);
        }

        return Html::tag('thead', Html::tag('tr', $cells));
    }

    protected function renderRow($row)
    {
        $cells = [];

        foreach ($this->columns as $key => $column) {
            if (! is_int($key) && isset($row->$key)) {
                $data = $row->$key;
            } else {
                $data = null;
                if (isset($column['column'])) {
                    if (is_callable($column['column'])) {
                        $data = call_user_func(($column['column']), $row);
                    } elseif (isset($row->{$column['column']})) {
                        $data = $row->{$column['column']};
                    }
                }
            }

            if (isset($column['renderer'])) {
                $content = call_user_func(($column['renderer']), $data, $row);
            } else {
                $content = $data;
            }

            $cells[] = Html::tag('td', $column['attributes'] ?? null, $content);
        }

        return Html::tag('tr', $cells);
    }

    protected function renderBody($data)
    {
        if (! is_array($data) && ! $data instanceof \Traversable) {
            throw new \InvalidArgumentException('Data must be an array or an instance of Traversable');
        }

        $rows = [];

        foreach ($data as $row) {
            $rows[] = $this->renderRow($row);
        }

        if (empty($rows)) {
            $colspan = count($this->columns);

            $rows = Html::tag(
                'tr',
                Html::tag(
                    'td',
                    ['colspan' => $colspan],
                    mt('__modulename__', 'No results found.')
                )
            );
        }

        return Html::tag('tbody', $rows);
    }

    protected function assemble()
    {
        $this->columns = $this->createColumns();

        $this->add(array_filter([
            $this->renderHeader(),
            $this->renderBody($this->getData())
        ]));
    }
}
