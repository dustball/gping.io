#!/bin/bash

if [ -z $GPINGIO_HOME ]; then
  echo GPINGIO_HOME must be set
  exit 1
fi

LOGDIR=$GPINGIO_HOME/.logs
LOGFILE=$LOGDIR/www-tests

rm -f $LOGFILE

echo "Checking for 'www' container."
if [ ! `docker ps -f name=www | wc -l` == "2" ]; then
  echo "  Did not find container, attempting to start."
  $GPINGIO_HOME/docker/www.sh > $LOGFILE 2>&1
  RESULT=$?
  if [ ! $RESULT == 0 ]; then
    echo "  Failed to start www container."
    echo
    cat $LOGFILE
    exit 1
  fi
fi

echo "Running tests"
echo

docker exec www /bin/bash -c "\
  cd /root/test; \
  phpunit --testdox-text /root/test/results.txt > /root/test/runlog.txt 2>&1; \
  cat /root/test/results.txt"

echo >> $LOGFILE
cat $GPINGIO_HOME/test/runlog.txt >> $LOGFILE
rm -f $GPINGIO_HOME/test/runlog.txt
mv $GPINGIO_HOME/test/results.txt $LOGDIR

echo
echo "Completed running unit tests."
echo "  Execution log: $LOGFILE"
echo "  Results:       $LOGDIR/results.txt"
