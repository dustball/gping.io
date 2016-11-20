#!/bin/bash

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
  gping.io:live
