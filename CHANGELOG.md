# Changes in Dockware

All notable changes of Dockware releases are documented in this file 
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [1.3.2] - 2020-10-22
### Added
 - Shopware 5.5.10 dev image
 - Big perfromance boost if xdebug is off
 
### Fixes
 - Make sure xdebug is safly disabled
 

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