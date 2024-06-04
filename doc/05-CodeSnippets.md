# Code Snipptes <a id="module-scaffoldbuilder-codesnippets"></a>

## Access Monitoring Data <a id="module-scaffoldbuilder-codesnippets-ido"></a>
### From a controller <a id="module-scaffoldbuilder-codesnippets-ido-controller"></a>
If you use monitoring data inside a controller the easiest way is to extend the monitoring controller:
```php
<?php

namespace Icinga\Module\Yourmodule\Controllers;

use Icinga\Module\Monitoring\Controller;

class IdoIndexController extends Controller {
    public function indexAction()
    {
        $this->backend = MonitoringBackend::instance($this->_getParam('backend'));
        $hosts = $this->backend->select()->from('hoststatus', array_merge(array(
            'host_icon_image',
            'host_icon_image_alt',
            'host_name',
            'host_display_name',
            'host_state' => $stateColumn,
            'host_acknowledged',
            'host_output',
            'host_attempt',
            'host_in_downtime',
            'host_is_flapping',
            'host_state_type',
            'host_handled',
            'host_last_state_change' => $stateChangeColumn,
            'host_notifications_enabled',
            'host_active_checks_enabled',
            'host_passive_checks_enabled',
            'host_check_command',
            'host_next_update'
        )));
        $this->applyRestriction('monitoring/filter/objects', $hosts);
        
         if ($hosts->count() > 0) {
            foreach ($hosts as $row) {
                $hostname = $row->host_name;
                $host = (array)$row;
            }
        }
    }
}
    
```
### From anywhere <a id="module-scaffoldbuilder-codesnippets-ido-anywhere"></a>

If you use monitoring data inside any class you can do this:

```php
<?php

namespace Icinga\Module\Yourmodulename;

use Icinga\Authentication\Auth;
use Icinga\Data\Filterable;
use Icinga\Exception\QueryException;
use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;

use Icinga\Data\Filter\Filter;


class IdoHelper
{

    public function getHost($hostname){
        $host = new Host($this->getBackend(), $hostname);
        $this->applyRestriction('monitoring/filter/objects', $host);
        if ($host->fetch() === false) {
            return null;
        }else{
            return $host;
        }
    }

    public function getService($hostname,$servicename){
        $service = new Service(
            $this->getBackend(),
            $hostname,
            $servicename
        );
        $this->applyRestriction('monitoring/filter/objects', $service);
        if ($service->fetch() === false) {
            return null;
        }else{
            return $service;
        }
    }
    protected function getBackend()
    {
        MonitoringBackend::clearInstances();

        return MonitoringBackend::instance();
    }
    /**
     * Apply a restriction of the authenticated on the given filterable
     *
     * @param   string      $name       Name of the restriction
     * @param   Filterable  $filterable Filterable to restrict
     *
     * @return  Filterable  The filterable having the restriction applied
     */
    protected function applyRestriction($name, Filterable $filterable)
    {
        $filterable->applyFilter($this->getRestriction($name));
        return $filterable;
    }

    /**
     * Get a restriction of the authenticated
     *
     * @param   string $name        Name of the restriction
     *
     * @return  Filter              Filter object
     * @throws  ConfigurationError  If the restriction contains invalid filter columns
     */
    protected function getRestriction($name)
    {
        $restriction = Filter::matchAny();
        $restriction->setAllowedFilterColumns(array(
            'host_name',
            'hostgroup_name',
            'instance_name',
            'service_description',
            'servicegroup_name',
            function ($c) {
                return preg_match('/^_(?:host|service)_/i', $c);
            }
        ));
        foreach ($this->getRestrictions($name) as $filter) {
            if ($filter === '*') {
                return Filter::matchAll();
            }
            try {
                $restriction->addFilter(Filter::fromQueryString($filter));
            } catch (QueryException $e) {
                throw new ConfigurationError(
                    t(
                        'Cannot apply restriction %s using the filter %s. You can only use the following columns: %s'
                    ),
                    $name,
                    $filter,
                    implode(', ', array(
                        'instance_name',
                        'host_name',
                        'hostgroup_name',
                        'service_description',
                        'servicegroup_name',
                        '_(host|service)_<customvar-name>'
                    )),
                    $e
                );
            }
        }

        if ($restriction->isEmpty()) {
            return Filter::matchAll();
        }

        return $restriction;
    }

    /**
     * Return restriction information for an eventually authenticated user
     *
     * @param   string  $name   Restriction name
     *
     * @return  array
     */
    public function getRestrictions($name)
    {
        return Auth::getInstance()->getRestrictions($name);
    }

}

    
```

## Access IcingaDB Data from a controller <a id="module-scaffoldbuilder-codesnippets-icingadb"></a>
### From a controller <a id="module-scaffoldbuilder-codesnippets-icingadb-controller"></a>

If you use monitoring data inside a controller the easiest way is to extend the icingdb controller:

```php
<?php

namespace Icinga\Module\Yourmodule\Controllers;

use Icinga\Module\Icingadb\Model\Host;
use Icinga\Module\Icingadb\Redis\VolatileStateResults;

class IcingadbIndexController extends Controller
{
    public function indexAction(){
        $db = $this->getDb();
        $hosts = Host::on($db)->with(['state', 'icon_image', 'state.last_comment']);
        $hosts->getWith()['host.state']->setJoinType('INNER');
        $hosts->setResultSetClass(VolatileStateResults::class); // for live data
        $this->applyRestrictions($hostQuery);

        $hosts = $hosts->execute();
        if (! $hosts->hasResult()) {
            return;
        }

        foreach ($hosts as $row) {
            $hostname = $row->name;
        }
    }
        
}

```
### From anywhere <a id="module-scaffoldbuilder-codesnippets-icingadb-anywhere"></a>

If you use monitoring data inside any class you can use the traits from IcingaDB:

```php
<?php

namespace Icinga\Module\Yourmodule\Controllers;

use Icinga\Module\Icingadb\Model\Host;
use ipl\Web\Compat\CompatController;
use Icinga\Module\Icingadb\Common\Auth;
use Icinga\Module\Icingadb\Common\Database;

class IcingadbIndexController extends CompatController
{
    use Database;
    use Auth;

    public function indexAction(){
        $db = $this->getDb() // from the trait Database
        $hosts = Host::on($db)->with(['state', 'icon_image', 'state.last_comment']);
        $hosts->getWith()['host.state']->setJoinType('INNER');
        $hosts->setResultSetClass(VolatileStateResults::class); // for live data
        $this->applyRestrictions($hosts); // from the trait Auth

        $hosts = $hosts->execute();
        if (! $hosts->hasResult()) {
            return;
        }

        foreach ($hosts as $row) {
            $hostname = $row->name;
        }
    }
        
}

```