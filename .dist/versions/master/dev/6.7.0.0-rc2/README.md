# dockware #dev

[![!](https://img.shields.io/badge/based%20on-production%20template-green.svg)]()

## Quick reference
Where to get help: https://www.dockware.io

Where to file issues: https://www.dockware.io

Documentation: https://dockware.io/docs

Maintained by: dasistweb GmbH (https://www.dasistweb.de)

More about Shopware: https://www.shopware.com

Shopware Platform on Github: https://github.com/shopware/platform

## What is dockware/dev?
dockware is a managed Shopware 6 docker image.

Run and explore any Shopware 6 version within minutes locally in docker.
No more hassle and blazing fast!

With dockware it's easy to explore Shopware and start developing in a smooth and seamless environment!

## How to use this image

### Quick Start
If you want to start the image and give it a try, simply
use the following command.

For the latest Shopware version use the tag `latest`, or use any other existing Shopware 6 version like `6.1.3`.

You can also switch to another PHP version by providing the ENV variable.

```ruby
# quick run with latest PHP and Shopware
$ docker run --rm -p 80:80 dockware/dev:6.7.0.0-rc2

# use another php version
$ docker run --rm -p 80:80 --env PHP_VERSION=7.2 dockware/dev:6.7.0.0-rc2
```

Please be aware that any modifications you make while Shopware is running, will be lost
when stopping and restarting the container.
This is really for easy plug and play tests.

If you want to keep your data, please read the information about persisting containers in this README.

### Environment Variables
This image comes with different features that can be set with the ENV variables.

| Feature  |  Default | Description |
|---|---| --- |
| PHP_VERSION  | 8.4 | Switch to any of the installed PHP versions:  8.3, 8.2,          |
| APACHE_DOCROOT  | /var/www/html/public | Sets the default DocRoot of Apache |
| SSH_USER	| not-set | Name of the optional new SSH user that replaces the existing one from dockware |
| SSH_PWD |	not-set	| Password of the optional new SSH user that replaces the existing one from dockware |
| MYSQL_USER |not-set | Optional variable to create a separate MySQL user. This is the name of the user. |
| MYSQL_PWD |not-set | Optional variable to create a separate MySQL user. This is the password of the user. |
| XDEBUG_ENABLED  | 0 | Enable or disable XDebug with either 1 or 0 as value. |
| XDEBUG_REMOTE_HOST | host.docker.internal | Use default value for MAC + Windows, and 172.17.0.1 for Linux |
| XDEBUG_CONFIG | idekey=PHPSTORM | IDE Key identifier for XDebug |
| PHP_IDE_CONFIG | serverName=localhost | used for the serverName export for XDebug usage on CLI |
| TIDEWAYS_KEY | not-set| API Key of the Tideways project |
| TIDEWAYS_ENV |	dev	| Optional identifier of the environment |
| COMPOSER_VERSION | not-set| Let's you switch between composer 1 and 2. |
| FILEBEAT_ENABLED | 0 | Activates the Filebeat daemon service (value 1). For this please provide a manual filebeat.yml for the container. You can do this with bind-mounting. |
| SW_CURRENCY | not-set | Switch to a different default currency for the system, like GBP. This will be used in the administration. |
| SW_API_ACCESS_KEY | not-set | Set a custom Storefront API key to access your Shopware API. |
| SW_TASKS_ENABLED | 0 | Enable the Scheduled Tasks and message consuming via the cron service by enabling this feature |

### Container Access
You can access the container either by using the docker exec command, or by using a ssh command.
If you want to access your container using SSH please make sure to expose port `22` (see docker-compose template below).

```ruby
# docker command
$ docker exec -it shopware bash

# ssh command with custom port
$ ssh dockware@localhost -p 22
```

Please use the following credentials for SSH or SFTP:

```ruby
user: dockware
password: dockware
Remote Path: /var/www/html
```

## Persistent data
The easy first run is perfect to immediately start Shopware, but does not persist any data or allows you to reuse that Shopware 6 instance again after restarting your host.
If you want to persist any data or changes you made, please create a volume and map these 2 folders to persist
the database and the DocRoot of Shopware.

* /var/lib/mysql
* /var/www/html

## Docker Compose Template
This is a full template with everything that can be done using dockware.
Please note that not all of these settings might be necessary.

```ruby
dockware:
    image: dockware/dev:6.7.0.0-rc2
    container_name: shopware
    ports:
        - "80:80"
        - "22:22"
        # Admin Watcher Port
        - "8888:8888"
        # Storefront Watcher Port
        - "9999:9999"
    environment:
        - PHP_VERSION=8.4
        - XDEBUG_ENABLED=1
        - SW_CURRENCY=GBP  
```

## Configuration Options

### Custom SSH User
It's possible to create a separate SSH access that replaces the one that comes out of the box with dockware.
For this, please provide the corresponding environment variables.
This will lead to an automatically created new SSH user when the docker container is launched.

```ruby
dockware:
  image: dockware/dev:6.7.0.0-rc2
  environment:
    - SSH_USER=userABC
    - SSH_PWD=supersecret
```

### Tideways Integration
Dockware comes with an installed Tideways agent.
You can enable this optional integration by simply providing your API key as environment variable.
In additional to this, you can add additional settings like the Tideways Environment identifiers.
Please keep in mind, that these settings might not be working for all Tideways packages (see Tideways for more).

```ruby
dockware:
  image: dockware/dev:6.7.0.0-rc2
  environment:
    - TIDEWAYS_KEY=xxx
    - TIDEWAYS_ENV=dev
```

### Filebeat Integration
The Filebeat integration allows you to automatically send log data and files to your Logstash instance if you use an ELK stack.

Please keep in mind, if Filebeat is enabled, you need a configured `filebeat.yml` file that is injected using bind-mounting.
In addition to this, your Docker container must have access to your logstash container by either using the links or networks in Docker.
This needs to be done only if you also have your Logstash within your Docker network :)

```ruby
dockware:
  image: dockware/dev:6.7.0.0-rc2
  volumes:
    - ./elk/myservice/filebeat.yml:/etc/filebeat/filebeat.yml
  environment:
    - FILEBEAT_ENABLED=1
```

## License

As with all Docker images, these likely also contain other software which may be under other licenses (such as Bash, etc from the base distribution, along with any direct or indirect dependencies of the primary software being contained).

As for any pre-built image usage, it is the image user's responsibility to ensure that any use of this image complies with any relevant licenses for all software contained within.
