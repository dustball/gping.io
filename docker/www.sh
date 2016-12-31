#!/bin/bash

NAMESPACE=$(whoami)
if [ ! -z $GPINGIO_NAMESPACE ]; then
  NAMESPACE=$GPINGIO_NAMESPACE
fi

if [ -z $NAMESPACE ]; then
  echo Unable to determine namespace for docker images
else
  NAMESPACE="$NAMESPACE/"
fi

WWW_CONTAINER=www
DB_CONTAINER=db

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

LINK="--link ${DB_CONTAINER}"
if [ "$1" == "--nodb" ]; then
  LINK=""
fi

cd ${GPINGIO_HOME}
docker kill ${WWW_CONTAINER}
docker rm ${WWW_CONTAINER}
docker run                             \
  -p 8080:80                           \
  --env-file docker/www/env            \
  --name ${WWW_CONTAINER}              \
  ${LINK}                              \
  -d                                   \
  -v ${GPINGIO_HOME}/www:/var/www/html \
  -v ${GPINGIO_HOME}/test:/root/test   \
  ${NAMESPACE}gping.io:live
