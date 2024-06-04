# Set up a work environment <a id="module-scaffoldbuilder-workenvironment"></a>

## Create an IcingaWeb2 instance

Of course, you can use anything else like docker and so on but if you have a VM running you collect monitoring data which
you can later use and connect to your module's logic.

> Everybody has a testing environment. Some people are lucky enough to have a totally separate environment to run production in.
>
>@stahnma

Set up Ubuntu 22.04 with ssh
connect to it and do the following:
```shell
sudo su
apt update
apt-get upgrade -y

wget -O - https://packages.icinga.com/icinga.key | gpg --dearmor -o /usr/share/keyrings/icinga-archive-keyring.gpg

. /etc/os-release; if [ ! -z ${UBUNTU_CODENAME+x} ]; then DIST="${UBUNTU_CODENAME}"; else DIST="$(lsb_release -c| awk '{print $2}')"; fi; \
 echo "deb [signed-by=/usr/share/keyrings/icinga-archive-keyring.gpg] https://packages.icinga.com/ubuntu icinga-${DIST} main" > \
 /etc/apt/sources.list.d/${DIST}-icinga.list
 echo "deb-src [signed-by=/usr/share/keyrings/icinga-archive-keyring.gpg] https://packages.icinga.com/ubuntu icinga-${DIST} main" >> \
 /etc/apt/sources.list.d/${DIST}-icinga.list
 
 
apt update

apt -y install apt-transport-https wget gnupg icinga2 vim monitoring-plugins icingadb icingadb-redis mariadb-server git




mysql_secure_installation
#Switch to unix_socket authentication [Y/n] Y
#Remove anonymous users? [Y/n] Y
#Disallow root login remotely? [Y/n] n
#Remove test database and access to it? [Y/n] Y
#Reload privilege tables now? [Y/n] Y

# make sure the hostname is ok before running icinga2 node wizard !


icinga2 node wizard
#Please specify if this is an agent/satellite setup ('n' installs a master setup) [Y/n]: n
#Please specify the common name (CN) [ubuntu]:
#Master zone name [master]:
#Default global zones: global-templates director-global
#Do you want to specify additional global zones? [y/N]: n
#Please specify the API bind host/port (optional):
#Bind Host []:
#Bind Port []:
#Do you want to disable the inclusion of the conf.d directory [Y/n]: Y -> we want to use director later

systemctl restart icinga2


mysql -e "CREATE DATABASE icingadb;
  CREATE USER 'icingadb'@'localhost' IDENTIFIED BY 'securePW123!';
  GRANT ALL ON icingadb.* TO 'icingadb'@'localhost';"

mysql icingadb </usr/share/icingadb/schema/mysql/schema.sql

#Set protected-mode to no, i.e. protected-mode no
sed -i 's/protected-mode yes/protected-mode no/' /etc/icingadb-redis/icingadb-redis.conf
systemctl enable --now icingadb-redis-server

# set host to 127.0.0.1
sed -i 's/host: localhost/host: 127.0.0.1/' /etc/icingadb/config.yml
# set password to securePW123!
sed -i 's/password: CHANGEME/password: securePW123!/' /etc/icingadb/config.yml

systemctl enable --now icingadb

icinga2 feature enable icingadb
systemctl restart icinga2

#set up via dbconfig-common
#MySQL application password for icinga2-ido-mysql: securePW123!
apt install icinga2-ido-mysql


apt -y install icingaweb2 libapache2-mod-php icingacli icingadb-web php-sqlite3
mysql -e "CREATE DATABASE icingaweb2;
  CREATE USER 'icingaweb2'@'localhost' IDENTIFIED BY 'securePW123!';
  GRANT ALL ON icingaweb2.* TO 'icingaweb2'@'localhost';"
  
icingacli setup token create
service apache2 restart
systemctl restart icingadb

# navigate to http://ip/icingaweb2
# use the token
# use the databases from the create statements
# for ido/monitoring it is icinga2 for database/user
# use 127.0.0.1 or localhost as host
# for logging use file
# for api use 127.0.0.1 root and the password from here
cat /etc/icinga2/conf.d/api-users.conf


cd /usr/share/icingaweb2/modules
git clone https://github.com/moreamazingnick/icingaweb2-module-icingalegacytheme
mv icingaweb2-module-icingalegacytheme icingalegacytheme

wget -q https://repos.influxdata.com/influxdata-archive_compat.key
echo '393e8779c89ac8d958f81f942f9ad7fb82a25e133faddaf92e15b16e6ac9ce4c influxdata-archive_compat.key' | sha256sum -c && cat influxdata-archive_compat.key | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/influxdata-archive_compat.gpg > /dev/null
echo 'deb [signed-by=/etc/apt/trusted.gpg.d/influxdata-archive_compat.gpg] https://repos.influxdata.com/debian stable main' | sudo tee /etc/apt/sources.list.d/influxdata.list
apt update
apt-get install influxdb2
systemctl enable influxdb
systemctl start influxdb


influx setup  --username admin --password 'securePW123!' --org icinga --bucket icinga2 --force 

influx config create --config-name icinga \
  --host-url http://localhost:8086 \
  --org icinga \
  --p admin:securePW123! \
  --active

influx bucket list
#create an auth token
influx auth create --write-bucket youricinga2bucket-id

echo '
object Influxdb2Writer "influxdb2" {
  host = "127.0.0.1"
  port = 8086
  organization = "icinga"
  bucket = "icinga2"
  auth_token = "YOURAUTHTOKEN"
  flush_threshold = 1024
  flush_interval = 10s
  host_template = {
    measurement = "$host.check_command$"
    tags = {
      hostname = "$host.name$"
    }
  }
  service_template = {
    measurement = "$service.check_command$"
    tags = {
      hostname = "$host.name$"
      service = "$service.name$"
    }
  }
  enable_send_thresholds = true
  enable_send_metadata = true

}
' > /etc/icinga2/features-available/influxdb2.conf

icinga2 feature enable influxdb2
systemctl restart icinga2

MODULE_VERSION="0.22.0"
ICINGAWEB_MODULEPATH="/usr/share/icingaweb2/modules"
REPO_URL="https://github.com/icinga/icingaweb2-module-incubator"
TARGET_DIR="${ICINGAWEB_MODULEPATH}/incubator"
URL="${REPO_URL}/archive/v${MODULE_VERSION}.tar.gz"
install -d -m 0755 "${TARGET_DIR}"
wget -q -O - "$URL" | tar xfz - -C "${TARGET_DIR}" --strip-components 1
icingacli module enable incubator

MODULE_VERSION="1.11.1"
ICINGAWEB_MODULEPATH="/usr/share/icingaweb2/modules"
REPO_URL="https://github.com/icinga/icingaweb2-module-director"
TARGET_DIR="${ICINGAWEB_MODULEPATH}/director"
URL="${REPO_URL}/archive/v${MODULE_VERSION}.tar.gz"

install -d -m 0755 "${TARGET_DIR}"
wget -q -O - "$URL" | tar xfz - -C "${TARGET_DIR}" --strip-components 1
icingacli module enable director
cd $TARGET_DIR
useradd -r -g icingaweb2 -d /var/lib/icingadirector -s /sbin/nologin icingadirector
install -d -o icingadirector -g icingaweb2 -m 0750 /var/lib/icingadirector
install -pm 0644 contrib/systemd/icinga-director.service /etc/systemd/system
systemctl daemon-reload
systemctl enable --now icinga-director

mysql -e "CREATE DATABASE director CHARACTER SET 'utf8';
  CREATE USER director@localhost IDENTIFIED BY 'securePW123!';
  GRANT ALL ON director.* TO director@localhost;"
  
  
echo '{ "HostTemplate": { "generic-host": { "check_command": "hostalive", "fields": [ { "datafield_id": 1, "is_required": "n", "var_filter": null } ], "object_name": "generic-host", "object_type": "template", "uuid": "8be076ec-e290-4a42-9539-55493b4be385" } }, "ServiceTemplate": { "generic-service": { "check_command": "dummy", "enable_active_checks": true, "enable_passive_checks": true, "enable_perfdata": true, "object_name": "generic-service", "object_type": "template", "uuid": "e053518d-a473-48fe-8105-f84a09c6e62e" } }, "ServiceSet": { "linux-basic": { "assign_filter": "host.vars.os=%22Linux%22", "object_name": "linux-basic", "object_type": "template", "services": [ { "check_command": "icinga", "imports": [ "generic-service" ], "object_name": "icinga2", "object_type": "object", "uuid": "e67480b7-f458-4490-a24c-e4977c167e87" }, { "check_command": "load", "imports": [ "generic-service" ], "object_name": "load", "object_type": "object", "uuid": "55f87741-4eab-47d0-aeed-27eed8e46950" }, { "check_command": "ping4", "imports": [ "generic-service" ], "object_name": "ping4", "object_type": "object", "uuid": "1145ca49-e522-4d44-af42-0f9e3cb436b9" }, { "check_command": "procs", "imports": [ "generic-service" ], "object_name": "procs", "object_type": "object", "uuid": "89f3e75e-759f-4f0b-841b-1d91f5eb6aab" }, { "check_command": "ssh", "imports": [ "generic-service" ], "object_name": "ssh", "object_type": "object", "uuid": "70e1c362-0c4f-460f-8f06-9e2f6ad65e00" }, { "check_command": "swap", "imports": [ "generic-service" ], "object_name": "swap", "object_type": "object", "uuid": "71a3fec2-1d9c-4346-9ce4-34f2d27b8b92" }, { "check_command": "users", "imports": [ "generic-service" ], "object_name": "users", "object_type": "object", "uuid": "dc11c4a2-1dd9-4e46-81a2-e734de13f935" } ], "uuid": "ba9e8af7-b4ec-42e6-ae78-1e1f2406d395" } }, "Datafield": { "1": { "uuid": "7f17a34e-5dad-4ec0-972f-4306ce5200ea", "varname": "os", "caption": "OS", "description": null, "datatype": "Icinga\\Module\\Director\\DataType\\DataTypeString", "format": null, "settings": { "visibility": "visible" }, "category": null } } }' > /tmp/basket.json
 
icingacli director basket restore --json < /tmp/basket.json 
 
icingacli director host create ubuntu  --object_type object --import generic-host --json '{ "address":"127.0.0.1", "imports":"generic-host", "vars.os": "Linux" }'

```
## Download the latest IcingaWeb2 files

* Go to https://github.com/Icinga/icingaweb2/releases/ and download the latest version of IcingaWeb2 or the version 
you prefer to write a module for.
* Go to https://github.com/Icinga/icinga-php-library/releases/ and download the latest version of icinga-php-library
or the version that is compatible with your IcingaWeb2 version
* Go to https://github.com/Icinga/icinga-php-thirdparty/releases and download the latest version of icinga-php-thirdparty
  or the version that is compatible with your IcingaWeb2 version


* Unpack IcingaWeb2 this will be used as project folder.
* create a folder inside your IcingaWeb2 folder named libs or anything you find appropriate
* Unpack icinga-php-library and icinga-php-thirdparty to this directory so PhpStorm will recognize it.



## IDE <a id="module-scaffoldbuilder-installation-requirements"></a>

I personally prefer PhpStorm but you can use any other IDE of your choice. 

### Create a new project in PhpStorm

### Connect to a IcingaWeb2 instance

## Create your first module
```shell
sudo icingacli scaffoldbuilder build --name test
```
### Download the module
#### Adapt your Sql files
#### Create a database
#### Create and link a resource
### Upload your changes

