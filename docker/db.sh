#!/bin/bash

DB_CONTAINER=db

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

NAMESPACE=$(whoami)
if [ ! -z $GPINGIO_NAMESPACE ]; then
  NAMESPACE=$GPINGIO_NAMESPACE
fi

if [ -z $NAMESPACE ]; then
  echo Unable to determine namespace for docker images
else
  NAMESPACE="$NAMESPACE/"
fi

cd ${GPINGIO_HOME}
docker kill ${DB_CONTAINER}
docker rm ${DB_CONTAINER}
docker run                 \
  --env-file docker/db/env \
  -p 3306:3306             \
  -d                       \
  --name ${DB_CONTAINER}   \
  ${NAMESPACE}gping.db:latest
