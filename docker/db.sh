#!/bin/bash

DB_CONTAINER=db

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

cd ${GPINGIO_HOME}
docker kill ${DB_CONTAINER}
docker rm ${DB_CONTAINER}
docker run                 \
  --env-file docker/db/env \
  -p 3306:3306             \
  -d                       \
  --name ${DB_CONTAINER}   \
  gping.db:latest
