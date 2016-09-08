# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.1 - 2016-09-08

### Added

- [#217](https://github.com/zendframework/ZendDeveloperTools/pull/217) adds
  support in the `SerializableException` for PHP 7 Throwables, including Error
  types.
- [#220](https://github.com/zendframework/ZendDeveloperTools/pull/220) adds
  support for displaying matched route parameters other than just the controller
  and action.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#215](https://github.com/zendframework/ZendDeveloperTools/pull/215) replaces
  the ZF logo to remove the "2".
- [#218](https://github.com/zendframework/ZendDeveloperTools/pull/218) updates
  the logic for retrieving a zend-db `Adapter` to only do so if `db`
  configuration also exists; this ensures the toolbar does not cause a fatal
  error if zend-db is installed but no adapter configured.

## 1.1.0 - 2016-06-27

### Added

- [#213](https://github.com/zendframework/ZendDeveloperTools/pull/213) adds
  support for zend-mvc, zend-eventmanager, and zend-servicemanager v3.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2016-06-27

First stable release.
