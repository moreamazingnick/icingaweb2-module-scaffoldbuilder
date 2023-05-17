# Installation <a id="module-__modulename__-installation"></a>

## Requirements <a id="module-__modulename__-installation-requirements"></a>

* Icinga Web 2 (&gt;= 2.10.3)
* Icinga Director (&gt;= 1.9.1)
* PHP (&gt;= 7.3)

The Icinga Web 2 `monitoring` module needs to be configured and enabled.

## Installation from .tar.gz <a id="module-__modulename__-installation-manual"></a>

Download the latest version and extract it to a folder named `__modulename__`
in one of your Icinga Web 2 module path directories.

## Enable the newly installed module <a id="module-__modulename__-installation-enable"></a>

Enable the `__modulename__` module either on the CLI by running

```sh
icingacli module enable __modulename__
```

Or go to your Icinga Web 2 frontend, choose `Configuration` -&gt; `Modules`, chose the `__modulename__` module and `enable` it.

It might afterwards be necessary to refresh your web browser to be sure that
newly provided styling is loaded.