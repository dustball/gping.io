# gping.io Server

This is the open-source server software for [gping.io](http://gping.io).

The client (Android app) is free with in-app purchasing. [Download at Google Play](https://play.google.com/store/apps/details?id=io.gping).

## Installation

1. Setup apache with mod_rewrite and PHP 
2. Clone repository into WWW path
2. Setup a MySQL database
3. Copy `config-dist.php` to `config.php` and edit 

## Structure

* `read.php` - parses URL and displays information, i.e. the web UI
* `write.php` - records pings from the device 
* `.htaccess` - routes web requests to the above
