#!/bin/bash

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

cd $GPINGIO_HOME
mkdir -p .logs

NAMESPACE=$(whoami)
if [ ! -z $GPINGIO_NAMESPACE ]; then
  NAMESPACE=$GPINGIO_NAMESPACE
fi

if [ -z $NAMESPACE ]; then
  echo Unable to determine namespace for docker images
else
  NAMESPACE="$NAMESPACE/"
fi

echo Building
echo "  - ${NAMESPACE}gping.db:latest"
docker build -t ${NAMESPACE}gping.db:latest -f docker/db/Dockerfile .  > .logs/docker-db.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-db.log
  echo
  echo container build failed
  exit 1
fi

echo "  - ${NAMESPACE}gping.io:live"
docker build -t ${NAMESPACE}gping.io:live -f docker/www/live.Dockerfile . > .logs/docker-www-live.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-www-live.log
  echo
  echo container build failed
  exit 1
fi

echo "  - ${NAMESPACE}gping.io:latest"
docker build -t ${NAMESPACE}gping.io:latest -f docker/www/Dockerfile .  > .logs/docker-www-latest.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-www-latest.log
  echo
  echo container build failed
  exit 1
fi
