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

## Setting up the Database

### Setting up a MySQL or MariaDB Database

The module needs a MySQL/MariaDB database with the schema that's provided in the `/usr/share/icingaweb2/modules/__modulename__/schema/mysql.schema.sql` file.

You can use the following sample command for creating the MySQL/MariaDB database. Please change the password:

```
CREATE DATABASE __modulename__;
GRANT CREATE, SELECT, INSERT, UPDATE, DELETE, DROP, ALTER, CREATE VIEW, INDEX, EXECUTE ON __modulename__.* TO __modulename__@localhost IDENTIFIED BY 'secret';
```

After, you can import the schema using the following command:

```
mysql -p -u root __modulename__ < /usr/share/icingaweb2/modules/__modulename__/schema/mysql.schema.sql
```

## Set a Database

Create the database and the resource usual and set is a database for the __modulename__ module in the modules preferences.
