# gping.io Server

This is the open-source server software for [gping.io](http://gping.io).

The client (Android app) is free with in-app purchasing for select features. [Download at Google Play](https://play.google.com/store/apps/details?id=io.gping).

## Installation

If you're interested in running your own dev server we've tried to make setup as
simple as possible by providing a set of containers. These collect all necessary
configuration for local development and should make it trivial to get a
functional production deployment.

For more detailed discussion of our container setup see [docker/README.md][dockerref].
In summary, however, to get a dev environment running:

1. Clone this repo
2. Define `GPINGIO_HOME` to be the cloned directory
3. `$GPINGIO_HOME> ./docker/build.sh`
4. `$GPINGIO_HOME> ./docker/db.sh`
5. `$GPINGIO_HOME> ./docker/www.sh`

You should now have an instance of the latest build running on `localhost:8080`.
A brief discussion of the helper scripts used is [here][scriptref]. If you're
interested in running a production instance see [this][prodref] for the changes
that shourd be made.

[dockerref]: docker/README.md#containers
[scriptref]: docker/README.md#conveniences
[prodref]: docker/Production.md

## Participate

We're on the [Freenode][fn] IRC network in `#gping.io` and there is a [forum][gg] for
questions.  If engineering is your cup of tea look at the [issue list][issues]
and our [roadmap][roadmap].

Our community guidelines are outlined in the [code of conduct][conduct].

[fn]: http://freenode.net/
[conduct]: conduct.md
[gg]: https://groups.google.com/forum/#!forum/gpingio
[issues]: https://github.com/dustball/gping.io/issues
[roadmap]: docs/Roadmap.md

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
