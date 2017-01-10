# Running GPing

There are two main ways to run a GPing production instance 1) manually place the
relevant files into a directory that is processed by PHP and 2) deploy from a
container directly. If you're familiar with Docker the second option will be
easier and is the suggested route but we'll cover both.

## via Apache / PHP

### Requirements

1. Your Apache server must have `mod_rewrite` enabled and allow config
   customizations via `.htaccess` files.
2. PHP >= v5.6+
3. Some SQL compatible DB (all development is done against MySQL 8.0 but the
   intent is to not use any proprietary features in the near future.
4. The following PHP extensions: `mysql`, `zip`.

### Deployment

The first step to deploying GPing is installing 3rd party dependencies. These
are managed by [Composer][composer] and are defined in `composer.json`. In
order to fetch dependencies run `composer install` from the same directory
as `composer.json`. After installation completes verify that
`www/vendor-composer` looks something like this:

    [~/Projects/gping.io/www] (master) $ ls -F vendor-composer
    autoload.php  composer/    lcobucci/    nikic/

Next you need to install your own `config.php`. This is where GPing pulls all
information that should vary by deployment. You should use `config-dist.php` as
the starting point for your deployed `config.php`. The meaning of each value
that is specified in this file is addressed in the _Configuration_ section.

Once configuration is done the `www` directory can be compressed and copied
into the product host's configured PHP document root.

[composer]: https://getcomposer.org/

## via Containers

The specifics will vary significantly depending on what you use for
orchestration but generally it will boil down to having your system:

1. `docker pull` the image you wish to deploy.
2. Configure the GPing service using the _Environment Variables_ approach.
3. Set up forwarding to the container's port 80.
4. Configure your network such that some load balancer / virtual IP routes to
   the containers and your containers have a route to your database.

Additionally you should make sure to set the number of instances your
orchestration system will maintain, their restart and autoscaling policies, etc.

### PHP runtime

Because a container encapsulates everything necessary to run a service it is
likely that you will want to customize the settings in [`php.ini`][php.ini]. A
list of the available php directives is found in [the manual][directives] and
many have their meaning is documented in a [sample file][src-php.ini] from PHP.

[php.ini]: ../docker/www/php.ini
[directives]: http://php.net/manual/en/ini.list.php
[src-php.ini]: https://github.com/php/php-src/blob/master/php.ini-production

## Configuration

### (Required) Service Config

**Hardcoding Data**

If taking the route of hard coding your deployment config data open the
deployed `config.php` file and search for `env('GPING_`. Each call to the env
function specifies the the config variable being referenced and a default value.
You can either remove the `env` call entirely or just update the default values.

For a discussion of each variable's meaning see the section below.

**Environment Variables**

If these values are set in the environment they will be used for the indicated
purpose.

Variable name        | Meaning
---------------------|--------------------
`GPING_JWT_ISSUER`   | When creating an authorization token this indicates who signed it.
`GPING_JWT_AUDIENCE` | When creating an authorization token this specifies what it is for.
`GPING_JWT_SECRET`   | A secret known only to the server that can be used to sigen a token to insure they may not be forged.
`GPING_DB_HOST`      | The hostname that acts as our database.
`GPING_DB_USER`      | The username used when connected to the database.
`GPING_DB_PASS`      | A password used to authenticated `GPING_DB_USER` to the database.
`GPING_DB_NAME`      | Specifies which database to use once connected.
`GPING_DEMO_ID`      | If location data is requested for this ID then the API knows to generate sample responses.
`GPING_DEMO_VIN`     | If the VIN associated with pushed data matches this then it will be replaced with a known stub value. This allows testing with your car without leaking what you drive.
`GPING_GMAP_API_KEY` | Used when making requests to the Google Maps API.

### (Optional) Logging Config

TBD. Tracked in [issue 25][iss25].

[iss25]: https://github.com/dustball/gping.io/issues/25

## Database

GPing requires a database for all interesting functionality. The schema is
currently defined in [`db/schema.sql`][ddl]. There is currently no tooling
in place to support database vertion migrations (tracked in [issue 17][iss17]).

[iss17]: https://github.com/dustball/gping.io/issues/17
[ddl]: ../db/schema.sql
