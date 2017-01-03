# gping.io Server

This is the open-source server software for [gping.io](http://gping.io).

The client (Android app) may be [downloaded at Google Play](https://play.google.com/store/apps/details?id=io.gping).

<a name="install"></a>
## Installation

### Docker

If you're interested in running your own dev server we've tried to make setup as
simple as possible by providing a set of containers. These collect all necessary
configuration for local development and should make it trivial to get a
functional production deployment.

For more detailed discussion of our container setup see [docker/README.md][dockerref].
In summary, however, to get a dev environment running:

1. Clone this repo
2. Define `GPINGIO_HOME` to be the cloned directory  
   _Recommended_: add `export GPINGIO_HOME='<path>'` to your `~/.profile`, `~/.bash_profile`
   or `~/.bashrc` then log out and back in for changes to take effect.
3. `cd $GPINGIO_HOME`
4. `cp www/config-dist.php www/config.php` and edit.
5. `./docker/build.sh`
6. `./docker/db.sh`  
   It may take a few moments for this container to fully spin up as the startup
   process includes bootstraping the DB. You can watch its progress with
   `docker logs -f db`.  
   _Note_: the first time you run this command it will produce `Error response
   from daemon`, this is normal.
7. `./docker/www.sh`
8. `docker exec www composer install`  
   This instructs [`composer`][composer] to download and install the libraries
   that the GPing service relies on.
9. _Optional_: List the running containers with `docker ps`.
10. _Optional_: Bring up the command line of the `www` container with:
   `docker/shell.sh www`

You should now have an instance of the latest build running on `localhost:8080`.
A brief discussion of the helper scripts used is [here][scriptref]. If you're
interested in running a production instance see [this][prodref] for the changes
that shourd be made.

[dockerref]: docker/README.md#containers
[scriptref]: docker/README.md#conveniences
[prodref]: docs/Production.md
[composer]: https://getcomposer.org/

### Apache

If you already have production Apache server ready to serve PHP files, you can
always deploy the `www/` directly under your htdocs.  Don't forget to `a2enmod
rewrite` in order to enable `mod_rewrite` in Apache and `composer install` to
get PHP library dependencies.

<a name="participate"></a>
## Participate

Our community guidelines are outlined in the [code of conduct][conduct].

- [Roadmap][roadmap]&mdash;Where we see GPing going and the large blocks of
  work that needs attention next.
- [Issues List][issues]&mdash;actionable tasks that need to be completed to
  reach some goal on our roadmap.
- [Forum][gg]&mdash;Ask questions to the community.

We also try to idle in the `#gping.io` IRC channel on [Freenode][fn].

[fn]: http://freenode.net/
[conduct]: conduct.md
[gg]: https://groups.google.com/forum/#!forum/gpingio
[issues]: https://github.com/dustball/gping.io/issues
[roadmap]: docs/Roadmap.md

<a name="structure"></a>
## Structure

Components of the project live loosely coupled under `$GPING_HOME` with
related code grouped by directory.

* [`db`](db)&mdash;DB schema and sample data
* [`docker`](docker)&mdash;container definitions and helper scripts around
  running dev setup
* [`docs`](docs)&mdash;various documentation; each major topic coordinated
  under `topic.md`
* [`test`](test)&mdash;test code; not included in the deployed build but
  mounted in the dev container
* [`www`](www)&mdash;website & backend; additional discussion in
  [www docs](docs/www.md)
