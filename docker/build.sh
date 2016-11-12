#!/bin/bash

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

cd $GPINGIO_HOME
echo Building DB image
docker build -t gping.db:latest -f docker/db/Dockerfile .

echo
echo
echo Building WWW image
docker build -t gping.io:latest -f docker/www/Dockerfile .
