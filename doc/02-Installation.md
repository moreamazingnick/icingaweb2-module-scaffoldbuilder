# Installation <a id="module-scaffoldbuilder-installation"></a>

## Requirements <a id="module-scaffoldbuilder-installation-requirements"></a>

* Icinga Web 2 (&gt;= 2.10.3)
* PHP (&gt;= 7.3)

## Installation from .tar.gz <a id="module-scaffoldbuilder-installation-manual"></a>

Download the latest version and extract it to a folder named `scaffoldbuilder`
in one of your Icinga Web 2 module path directories.

## Enable the newly installed module <a id="module-scaffoldbuilder-installation-enable"></a>

Enable the `scaffoldbuilder` module either on the CLI by running

```sh
icingacli module enable scaffoldbuilder
```

Or go to your Icinga Web 2 frontend, choose `Configuration` -&gt; `Modules`, chose the `scaffoldbuilder` module and `enable` it.

It might afterwards be necessary to refresh your web browser to be sure that
newly provided styling is loaded.