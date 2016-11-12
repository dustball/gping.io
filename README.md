# gping.io Server

This is the open-source server software for [gping.io](http://gping.io).

The client (Android app) is free with in-app purchasing. [Download at Google Play](https://play.google.com/store/apps/details?id=io.gping).

## Installation

1. Setup apache with mod_rewrite and PHP 
2. Clone repository into WWW path
3. Setup a MySQL database
4. Run `mysql db_name < db-schema.sql`
5. Copy `config-dist.php` to `config.php` and edit 

## Structure

* `read.php` - parses URL and displays information, i.e. the web UI
* `write.php` - records pings from the device 
* `.htaccess` - routes web requests to the above

## Participate

We're on the [Freenode][fn] IRC network in `#gping.io`, there is a [forum][gg] for
questions, and shortly we should have a list of upcoming feature work if engineering
is your cup of tea.

Our community guidelines are available in the [code of conduct][conduct].

[fn]: http://freenode.net/
[conduct]: conduct.md
[gg]: https://groups.google.com/forum/#!forum/gpingio
