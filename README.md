# simple-ldap-manager

A simple LDAP manager

## Requirements

PHP 5.5

## Install

### Ubuntu - Apt

If needed, replace *trusty* with the name of your Ubuntu release.

```sh
$ sudo su -c 'echo "deb http://dl.bintray.com/v1/content/lucidsoftware/apt trusty main" > /etc/apt/sources.list.d/lucidsoftware.list'
$ sudo apt-get update
$ sudo install simple-ldap-manager
```

If you would like the last build of the `master` branch, replace *main* with *dev*.

## Build

`make package` creates a debian package

[![Build Status](https://travis-ci.org/lucidsoftware/simple-ldap-manager.svg)](https://travis-ci.org/lucidsoftware/simple-ldap-manager)
