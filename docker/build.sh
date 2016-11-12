#!/bin/bash

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

cd $GPINGIO_HOME
mkdir -p .logs

echo Building
echo "  - gping.db:latest"
docker build -t gping.db:latest -f docker/db/Dockerfile .  > .logs/docker-db.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-db.log
  echo
  echo container build failed
  exit 1
fi

echo "  - gping.io:live"
docker build -t gping.io:live -f docker/www/live.Dockerfile . > .logs/dacker-www-live.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-www-live.log
  echo
  echo container build failed
  exit 1
fi

echo "  - gping.io:latest"
docker build -t gping.io:latest -f docker/www/Dockerfile .  > .logs/docker-www-latest.log 2>&1
if [ ! $? -eq 0 ]; then
  cat .logs/docker-www-live.log
  echo
  echo container build failed
  exit 1
fi
