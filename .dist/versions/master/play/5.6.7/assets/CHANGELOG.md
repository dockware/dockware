# Changes in Dockware

All notable changes of Dockware releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [UNRELEASED]

### Added
- Add PHP 8.2 to all images 
- Added missing PHP extension "apc" in all PHP versions 
- Add new "make restart-php" command to restart FPM + Apache. 
- Add new ENV variable "SHOP_DOMAIN" to automatically change Sales Channel domains on startup.

### Fixed
- Fixed broken with Apache in Entrypoint. Sometimes Apache cannot start because port 80 is blocked.
- Fixed wrong installation of PHP extensions "geoip" and "amqp". These were accidentally only installed for the current PHP version.
- Fix broken Tideways service 
- Fix rare problems with switching Composer versions. Composer is now only changed after a correct switch to the required PHP version. 

## [1.5.4]

### Added

- Add the storefront watcher, admin watcher, and build admin commands to the essentials image
- Add SSH2 PHP extension 
- Add PCOV for code coverage to PHP version starting vom 7.1

### Fixed
- Fixed broken Mailcatcher
- Fixed node and yarn problems in combination with NVM
- Fix problem with npm, node and yarn for sudo users
- Fix problem with XDebug after switching PHP version
- Fix problem with XDebug status directly on bootup. This was just not working sometimes.

## [1.5.3]

### Added

- Add new ENV variable NODE_VERSION and "nvm" to switch between Node v12, v14 and v16

### Fixed

- Fixed broken XDebug after switching PHP versions.
-

## [1.5.2]

### Fixed

- Fix problem where it was not possible to provide a custom WORKDIR. The images will now switch back to this directory in the entrypoint before running custom commands.

## [1.5.1]

### BREAKING CHANGES FOR CI/CD PIPELINES

With 1.5.1 with changed the entrypoint to use the real ENTRYPOINT command instead of the CMD command in the Dockerfile.
The previous version allowed you to provide a custom command (e.g. in your pipelines) to be executed within dockware.
This worked, but did not use the original entrypoint, which means that neither the PHP_VERSION switch, launch of MySQL or other things worked.
The new change does always load the original entrypoint along all its configured features of dockware.

This however would lead to a blocking long-running container with Shopware.
If you still want to just execute your single command in dockware within your pipeline,
please provide the env variable DOCKWARE_CI=1 and the container will automatically quit as soon as your command has been executed

### Added

- Add new ENV variable DOCKWARE_CI to automatically stop the container, once your custom command in your pipeline has been executed with dockware.
- Add Node v16 to Shopware >= 6.4 and Shopware >= 5.7. All previous versions still have Node v12.

### Improved:

- Shopware is now installed as www-data in a clean way. So all permissions of all files should be as clean as possible now when launching containers.

### Fixed

- Fixed broken XDebug scripts from previous releases
- Fixed "Plugin Build" mode that did not find the bin/build-js.sh file correctly.
- Fixed wrong warning outputs of Apache when starting the containers. This did not have any impact. It was just a weird warning output.
- Fixed problem with broken permissions after using the storefront watcher with our makefile

## [1.5] - 2022-04-01

### Added

- Add new ENV variable SW_API_ACCESS_KEY to provide a ready to use Storefront API key from your Docker setups.
- Add new ENV variable SW_TASKS_ENABLED to automatically run Scheduled Tasks and consume the Message Queues.
- Add PHP Extension "ampqp" to allow better messaging integrations.
-

### Changed

- Updated to Composer v2.2.9
- Removed watcher commands for essentials image, because there is no Shopware installed, and they commands are different across Shopware versions.

## [1.4.3] - 2021-11-25

### Added

- Add support for PHP 8.1 in all appropriate images
- Add NANO editor to images
- NANO and VIM are now available in all images and not only in dev-images
- Add "PHP Version Switching" as easy makefile command in /var/www
- Add "IMAP" PHP extensions to all PHP versions in all images

## [1.4.2] - 2021-10-13

### Added

- Added "jq" package for JSON processing
- Add PHP 5.6 support to essentials and flex image

### Fixed

- Composer was accidentally always updated, even if no ENV variable has been provided
- Fix problems with XDEBUG on PHP 5.6

## [1.4.1] - 2021-09-14

### Added

- Added new "make download" command to easily prepare the installation of Shopware in the "essentials" image.

### Fixed

- Fixed missing demo data in some Shopware 5 images.
- Fixed problem with wrong function definer with custom MySQL user (https://github.com/dockware/dockware/issues/58)

## [1.4] - 2021-09-01

### Added

- Added feature to only build a mounted plugin without running Shopware itself.
- Added "sodium" PHP extension.
- Added real Demo Data plugin for Shopware > 6.2 versions
- Add ready to use "base.scss" file to Dockware Sample Plugin

### Changed

- Improved "PimpMyLogs" entries for Shopware 5 and Shopware 6
- Use Composer v1 before Shopware 6.4
- Improved Watchers across different Shopware versions

### Fixed

- Fixed missing Shopware 5 default Mailer settings for Mailcatcher

## [1.3.5] - 2021-03-10

### Added

- Add PHP 8.0
- Add Xdebug 3
- Add Compose 2

### Changed

- Switched to "trunk" branch in contribution

### Fixed

- Fix broken database permissions on used triggers if a custom database user is used.
  This led to problems when creating e.g. products in the Administration.
- Fix problem where mysqld.sock.lock file was not correctly deleted on bootup, which could
  lead to the issue that the MySQL fails when starting.

## [1.3.4] - 2020-11-12

### Fixed

- Remove wrong "/" path in Shopware 5 images
- set home dir for users to /var/www to avoid watcher permission bugs

## [1.3.3] - 2020-11-02

### Added

- made logging from cli & fpm constistent
- made images smaller
- ebalbed opcache per default
- custom ssh dosen't require sudo pwd anymore
-

### Fixed

- CLI Logging now works
- located makefile again under /var/wwww
- fixed switching xdebug
- fixed restarting with custom ssh user
- fixed makefule essentials (watch commands)
- fixed session permissions on /etc/..

## [1.3.2] - 2020-10-22

### Added

- Shopware 5.5.10 dev image
- Big perfromance boost if xdebug is off
- make commands for en/disable xdebug without restart can be found in **var/www/scripts**
- composer 2.0

### Fixes

- Make sure xdebug is safly disabled

### removed

- hirak/prestissimo (not lomger needed as we have now composer 2.0)

## [1.3.1] - 2020-10-16

### Added

- Add php-fpm
- Add mpm_event

### Removed

- Add mod_php
- Add mpm_prefork

### Fixes

- Fix broken Storefront Watcher due to wrong Apache Document directory
- Fix broken Storefront Watcher script in dockware/contribution due to missing second request header.

## [1.3.0] - 2020-10-08

### Added

- Brand new "dockware/flex" image
- New Tideways integration
- New Filebeat integration
- Apache DocRoot environment setting
- Option to create a custom SSH users and remove the default SSH user
- Option to create a custom MySQL user and disable remote access for "root"
- Set your custom timezone with the "TZ" environment variable

## [1.2.1] 2020-09-22

### Changed

* Fixes watchers for Storefront and Admin for latest Shopware versions

## [1.2.0] 2020-07-06

### Added

* Added first Shopware 5 dockware images

## [1.1.0] 2020-06-29

### Added

* Added new dockware/essentials image

## [1.0.0] 2020-06-19

### Added

* Added initial version of dockware/play, dockware/dev and dockware/contribute
