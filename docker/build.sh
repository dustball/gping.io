#!/bin/bash

ROOTDIR=${GPINGIO_HOME:-.}

echo Building DB image
docker build -t gping.db:latest -f docker/db/Dockerfile ${ROOTDIR}

echo
echo
echo Building WWW image
docker build -t gping.io:latest -f docker/www/Dockerfile ${ROOTDIR}
