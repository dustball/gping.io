# gping.io in Space (or the cloud)

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

[composer]: https://getcomposer.org/

## via Containers

The specifics will vary significantly depending on what your use for
orchestration but generally it will boil down to having it:

1. `dockerpull` the image you wish to deploy.
2. Configure the GPing service using the _Environment Variables_ approach.
3. Set up forwarding to the container's port 80.
4. Configure your network such that some load balancer / virtual IP routes to
   the containers.

Additionally you should make sure to set the number of instances your
orchestration system will maintain, their restart and autoscaling policies, etc.

### PHP runtime

Because a container encapsulates everything necessary to run a service it is
likely that you will want to specify a new configuration for the existing
[`php.ini`][php.ini]. Suggested changes include disabling error output

[php.ini]: ../docker/www/php.ini

## Configuration

### (Required) Service Config

**Hardcoding Data**

**Environment Variables**

### (Optional) Logging Config

