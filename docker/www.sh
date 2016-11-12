#!/bin/bash

WWW_CONTAINER=www
DB_CONTAINER=db
ROOT=${GPINGIO_HOME}

cd ${ROOT}
docker kill ${WWW_CONTAINER}
docker rm ${WWW_CONTAINER}
docker run                  \
  -p 8080:80                \
  --env-file docker/www/env \
  --name ${WWW_CONTAINER}   \
  --link ${DB_CONTAINER}    \
  -d                        \
  gping.io:latest
