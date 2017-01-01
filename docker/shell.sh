#!/bin/bash

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi


CMD=$(basename $0)

function usage {
  echo "Usage: $CMD {db, www, mysql}"
  echo
  echo "$CMD provides easy access to a running docker container."
  echo
  echo "  db    - bash shell in the database container"
  echo "  www   - bash shell in the apache container"
  echo "  mysql - start mysql client session connecting to the db"
}

if [ -z $1 ]; then
  usage
  exit 1
fi

case "$1" in
  db)
    shift
    docker exec -ti db bash
    ;;

  www)
    shift
    docker exec -ti www bash
    ;;

  mysql)
    shift
    source ${GPINGIO_HOME}/docker/db/env
    docker exec -ti db mysql -h localhost -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}
    ;;

  *)
    usage
    exit 1
    ;;
esac
