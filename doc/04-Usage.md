# Usage <a id="module-scaffoldbuilder-usage"></a>

## Module Usage  <a id="module-scaffoldbuilder-usage"></a>

This module provides a CLI interface only. Here are some examples of generating code:

### Generating a module  <a id="module-scaffoldbuilder-usage-generate-module"></a>

```shell
sudo icingacli scaffoldbuilder build --name yourmodulename
```

### Generating a theme  <a id="module-scaffoldbuilder-usage-generate-theme"></a>

In case you want to have a custom theme for IcingaWeb2 shipped with your module you can use:
```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --theme yourthemename
```

### Generating a Filemanager   <a id="module-scaffoldbuilder-usage-generate-filemanager"></a>

In case you want to have a one directory filemanager shipped with your module you can use:
```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --filemanager YES
```

### Generating a Ini-Repository   <a id="module-scaffoldbuilder-usage-generate-filemanager"></a>

IniRepositories are ini-based "databases". You should have already seen this when generating resources in IcingaWeb2 or
if you use the elastic search module.
This example generated code for users and groups. Users are shown in a table view and groups are shown in a grid view.

```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --iniconfigs "user:table, group:grid"
```

### Generating a Sql-Database   <a id="module-scaffoldbuilder-usage-generate-sql"></a>

For Sql-Databases scaffoldbuilder can render the Model/View/Controller files for you.
This example generated code for users and groups. Users are written to the table `yourmodulename_user` and 
groups are written to the table `yourmodulename_group`. Since User is a MySQL keyword you should always use a table prefix.

```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --dbconfigs "user:user, group:group"
```

If you prefer a different table prefix you can specify this with `--tableprefix` but by default it is the modulename.

```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --dbconfigs "user:user, group:group" --tableprefix "tbl_"
```

### Generating a SqLite-Helper   <a id="module-scaffoldbuilder-usage-generate-sqlite"></a>

IcingaWeb2 does not come with Sqlite Database migration so this helper allows you to change your database inside the module itself.
Remove this code when in production.

```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --sqlite YES
```

## Developer Options  <a id="module-scaffoldbuilder-dev"></a>

### Modulepath <a id="module-scaffoldbuilder-dev-modulepath"></a>

IcingaWeb2 allows you to add multiple module paths <a href='/icingaweb2/config/general'>here</a>. For example:
> /usr/share/icingaweb2/modules:/home/icingaweb2/modules

To generate modules in this second path use:
```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --modulepath /home/icingaweb2/modules
```

### Permissions <a id="module-scaffoldbuilder-dev-modulepath"></a>

In earlier version sof PhpStorm (< 2024) it was not possible to upload in a "sudo" way, so if you use the --dev parameter the
permissions of your module will be set to 0777.

To generate modules in this second path use:
```shell
sudo icingacli scaffoldbuilder build --name yourmodulename --dev YES
```