# Changelog

All notable changes to `laravel-dom-assertion` will be documented in this file.

## v2.6.0 - 2025-11-17

* Add assertSelect and assertSelectExists macros #39 (by @MizouziE )

## v2.5.0 - 2025-10-09

* Cache parser in the container to speed up tests - by @jackbayliss
* Use correct classes in ide-helper.php - by @jackbayliss

## v2.4.0 - 2025-10-06

* Add assertContainsElement #35 by @jackbayliss

## v2.3.0 - 2025-09-30

* Give each() callback access to foreach index #33 by @MizouziE
* Include DOM path when nesting in assertions #34 by @jackbayliss

## v2.2.0 - 2025-09-23

* Add assertElement and assertForm aliases #29 by @jackbayliss
* fix phpstan #28 by @jackbayliss

## v2.1.0 - 2025-06-27

Add support for testing Blade components - by @ziadoz

## v2.0.0 - 2025-03-31

Laravel 12 support (@laravel-shift and @jackbayliss)

## v1.5.4 - 2024-08-28

Add missing ide helpers for TestView (by @markieo1)

## v1.5.3 - 2024-06-17

Re-release as last release was broken for some reason

## v1.5.2 - 2024-06-17

* Implement each method to allow fluent assertions against all elements that match selector (by @FRFlor https://github.com/sinnbeck/laravel-dom-assertions/pull/22 )
* Fix wrong method used in readme (by @FRFlor )

## v1.5.1 - 2024-06-07

* Workaround for bug in php8.3.8 https://github.com/sinnbeck/laravel-dom-assertions/issues/19

## v1.5.0 - 2024-03-13

* Bump dependencies for Laravel 11 by @laravel-shift
* Update GitHub Actions for Laravel 11 by @laravel-shift

## v1.4.1 - 2024-01-03

* Allow using symfony css selector v.7.0. Fixes https://github.com/sinnbeck/laravel-dom-assertions/issues/15

## v1.4.0 - 2024-01-03

* Add possibility to test blade views by @helloiamlukas https://github.com/sinnbeck/laravel-dom-assertions/pull/14

## v1.3.1 - 2023-08-02

- Add optgroup support (by @ziadoz)

## v1.3.0 - 2023-02-14

- Add laravel 10 support

## v1.2.0 - 2023-02-13

- Add `containsText()` assertion. (thanks to @balping )

## v1.1.2 - 2023-02-10

- Add `->doesntHave()` assertion

## v1.1.0 - 2022-11-10

- Normalize multi-line text https://github.com/sinnbeck/laravel-dom-assertions/pull/9
- Prepare for head assertions

## v1.1.1 - 2022-11-10

- Add datalist assertions
- Add html5 assertion

## v1.0.4 - 2022-10-27

- remove internal tag from service provider as it caused issues in other packages

## 1.0.3 - 2022-10-26

- Fixes bug where `$selector` is called as closure

## v1.0.2 - 2022-10-25

- Remove `@internal` from externally accessible classes

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
