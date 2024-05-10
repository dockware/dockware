# dockware #play

[![!](https://img.shields.io/badge/based%20on-production%20template-green.svg)]()

## Quick reference
Where to get help: https://www.dockware.io

Where to file issues: https://www.dockware.io

Documentation: https://dockware.io/docs

Maintained by: dasistweb GmbH (https://www.dasistweb.de)

More about Shopware: https://www.shopware.com

Shopware Platform on Github: https://github.com/shopware/platform

## What is dockware/play?
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
$ docker run --rm -p 80:80 dockware/play:6.6.2.0

# use another php version
$ docker run --rm -p 80:80 --env PHP_VERSION=7.2 dockware/play:6.6.2.0
```

Please be aware that any modifications you make while Shopware is running, will be lost
when stopping and restarting the container.
This is really for easy plug and play tests.

If you want to keep your data, please read the information about persisting containers in this README.

### Environment Variables
This image comes with different features that can be set with the ENV variables.

| Feature  |  Default | Description |
|---|---| --- |
| PHP_VERSION  | 8.3 | Switch to any of the installed PHP versions: 8.3, 8.2,          |
| COMPOSER_VERSION | not-set| Let's you switch between composer 1 and 2. |
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
shopware:
    image: dockware/play:6.6.2.0
    container_name: shopware
    ports:
        - "80:80"
        - "443:443"
        - "22:22"
    environment:
        - PHP_VERSION=8.3
```

## License

As with all Docker images, these likely also contain other software which may be under other licenses (such as Bash, etc from the base distribution, along with any direct or indirect dependencies of the primary software being contained).

As for any pre-built image usage, it is the image user's responsibility to ensure that any use of this image complies with any relevant licenses for all software contained within.
