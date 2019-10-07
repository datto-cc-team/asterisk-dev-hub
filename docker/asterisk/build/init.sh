#!/bin/sh
./mysql-odbc-connector.sh

/sync-config/run.php

# the run.php script above runs in a loop, so we should never get here
# keep docker container alive
touch /keepalive
tail -f /keepalive
