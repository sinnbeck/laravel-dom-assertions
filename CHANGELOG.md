# Changelog

All notable changes to `laravel-dom-assertion` will be documented in this file.

## v1.0.0 - 2022-10-25

First stable release

## v0.3.5 - 2022-10-24

- Add support for : in attributes (eg. `wire:click="doStuff"`)

## v0.3.4 - 2022-10-24

- Fix has() assertion bug
- Add phpstan
- Clean up folder structure

## v0.3.3 - 2022-10-23

- Huge refactor to make it easier to work on
- Fix assertion error messages
- Allow comparing partial classes

## v0.3.2 - 2022-10-22

- Add dd() and dump() helpers
- Handle broken html in views better

## v0.3.1 - 2022-10-21

- Fix method spoof
- Allow count in contains()
- Fix bug in array comparison between attributes

## v0.3 - 2022-10-20

- Rewrite several asserts to clean up code
- Rewrite docs
