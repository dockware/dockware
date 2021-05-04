# Changes in Dockware

All notable changes of Dockware releases are documented in this file 
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [UNRELEASED] 

### Changed
- Improved "pimpmylogs" entries for Shopware 5 and Shopware 6

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