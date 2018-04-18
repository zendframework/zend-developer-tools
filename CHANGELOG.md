# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.2.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.0 - 2018-04-17

### Added

- [#242](https://github.com/zendframework/ZendDeveloperTools/pull/242) adds support for PHP 7.1 and 7.2.

### Changed

- [#235](https://github.com/zendframework/ZendDeveloperTools/pull/235) modifies the module bootstrap to defer retrieval of services until they are needed.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#240](https://github.com/zendframework/ZendDeveloperTools/pull/240) fixes an issue with slide-in of the toolbar when resizing the browser window.

- [#231](https://github.com/zendframework/ZendDeveloperTools/pull/231) ensures literal `$` characters are escaped within toolbar content.

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
