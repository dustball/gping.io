# Docker and gping.io

## What is Docker, a crash course

A [Docker][docker] container can be thought of as a snapshot of a functioning
system. This snapshot has commands it knows to run on start up and can be
executed by a virtualization engine like [VirtualBox][vb].  This allows us to
construct completly self contained systems that bundle all necessary
dependencies and move them around between developers.

Because the container executes as a virtual machine it can be run without
modification on a Linux, Windows, or Mac host operating system. Additionally it
becomes easier to reason about allocating services (that are encapsulated by a
container) to resources in a cloud computing environment. To that end
[Google Kubernetes][k8s], [Amazon ECS][ecs], and others have built systems
allowing trivial deployment of container applications.

Containers introduce some new problems however in the realm of persistent
storage and networking. There are solutions but they are beyond the scope of
this doc and often are tightly integrated with the particular execution engine
being used (dev machine virtualization, AWS ECS, etc.).

Get docker from [here][get-docker], there is also a lot of additional
information there if you want to know more about how it works.

[docker]: https://www.docker.com/
[vb]: https://www.virtualbox.org/
[k8s]: http://kubernetes.io/
[ecs]: https://aws.amazon.com/ecs/
[get-docker]: https://www.docker.com/products/overview

<a name="containers"></a>
## gping.io Containers

To ease development a few containers are defined for `gping.io`:

- `gping.io:latest`&mdash;contains a functioning Apache webserver and PHP
   interpreter;
- `gping.io:live`&mdash;contains the same Apache / PHP combination as the
   `latest` tag but sources the actual website from a directory mounted from
   the host operating system to ease development;
- `gping.db:latest`&mdash;is a database server running MySQLv8 and preloaded
   with the `gping` tables; due to the nature of containers this should _not_
   be used for persistent data as it will be lost at container termination.

`gping.io:latest` should be a suitable container for deployment to a production
environment though no customization of the PHP runtime has been done. A minimal
config will be put into place and is located at `docker/www/php.ini`.

All containers are configured via environment variables located at
`docker/{container}/env`.

<a name="conveniences"></a>
## Conveniences

A few scripts exist to make life a bit easier:

- `docker/build.sh`&mdash;this will build a new `db` and `www` container from
   the current `gping.io` tree.
- `docker/db.sh`&mdash;starts an instance of a dev database using the default
   configurations from `docker/db/env`.
- `docker/www.sh`&mdashstarts an instance of the `gping.io` website with
   routing to a dev database using default configurations in `docker/www/env`
   and the `live` tag by default.

All scripts expect an environment variable `GPINGIO_HOME` to be set to the
directory containing the project checkout.

The `www` container depends on `db` so the launch order must be 1) `docker/db.sh`
2) `docker/www.sh`. You can verify both containers are running with `docker ps`:

```
$ docker ps
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                    NAMES
7036297070dd        gping.io:live       "apache2-foreground"     3 hours ago         Up 3 hours          0.0.0.0:8080->80/tcp     www
99150e314044        gping.db:latest     "docker-entrypoint.sh"   3 hours ago         Up 3 hours          0.0.0.0:3306->3306/tcp   db
```

Accessing the container depends on which version of Docker is being run but the
most recent versions will automatically forward loopback to the containers on
the forwarded ports. As such in the example above you can hit
[localhost:8080][lh] to view the app and can connect mysql clients to
`localhost:3306`.

In the event you're running an older Docker version that does not handle
localhost -> container forwarding there will be an IP associated with the
virtual machine acting as host to the containers that can _usually_ be found
via: `docker-machine ip default`.

[lh]: http://localhost:8080/
