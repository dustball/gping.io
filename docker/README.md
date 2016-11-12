# Docker and gping.io

## What is Docker, a crash course

A [Docker][docker] container can be thought of as a snapshot of a functioning system. This
snapshot has commands it knows to run on start up and can be executed by a virtualization
engine like [VirtualBox][vb].  This allows us to construct completly self contained
systems that bundle all necessary dependencies and move them around between developers.

Because the container executes as a virtual machine it can be run without modification on a
Linux, Windows, or Mac host operating system. Additionally it becomes easier to reason about
allocating services (that are encapsulated by a container) to resources in a cloud computing
environment. To that end [Google Kubernetes][k8s], [Amazon ECS][ecs], and others have built
systems allowing trivial deployment of container applications.

Containers introduce some new problems however in the realm of persistent storage and
networking. There are solutions but they are beyond the scope of this doc and often are
tightly integrated with the particular execution engine being used (dev machine virtualization,
AWS ECS, etc.).

[docker]: https://www.docker.com/
[vb]: https://www.virtualbox.org/
[k8s]: http://kubernetes.io/
[ecs]: https://aws.amazon.com/ecs/
[dockerwiki]: https://en.wikipedia.org/wiki/Docker_(software)

## gping.io Containers

To ease development two containers are defined for `gping.io`:

- `gping.io`&mdash;contains a functioning Apache webserver and PHP interpreter
- `gping.db`&mdash;is a database server running MySQLv8 and preloaded with the
  `gping` tables; due to the nature of containers this should _not_ be used for
  persistent data as it will be lost at container termination.

`gping.io` should be a suitable container for deployment to a production environment though
no customization of the PHP runtime has been done. A minimal config will be put into place
and is located at `docker/www/php.ini`.

Both containers are configured via environment variables located at `docker/{container}/env`.

## Conveniences

A few scripts exist to make life a bit easier:

- `docker/build.sh`&mdash;this will build a new `db` and `www` container from the current
  `gping.io` tree.
- `docker/db.sh`&mdash;starts an instance of a dev database using the default configurations
  from `docker/db/env`.
- `docker/www.sh`&mdashstarts an instance of the `gping.io` website with routing to a dev
  database using default configurations in `docker/www/env`.

All scripts expect an environment variable `GPINGIO_HOME` to be set to the directory containing
the project checkout.

The `www` container does not currently support live code loading through volume mounts or
other means (patches welcome :heart:).