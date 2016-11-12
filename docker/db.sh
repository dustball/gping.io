#!/bin/bash

DB_CONTAINER=db
ROOT=${GPINGIO_HOME}

cd ${ROOT}
docker kill ${DB_CONTAINER}
docker rm ${DB_CONTAINER}
docker run                 \
  --env-file docker/db/env \
  -p 3306:3306             \
  -d                       \
  --name ${DB_CONTAINER}   \
  gping.db:latest
