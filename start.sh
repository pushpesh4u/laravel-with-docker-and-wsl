#!/bin/sh
ls -lah | wc -l

BASEDIR=$(dirname "$0")

SAIL_PATH="$BASEDIR/vendor/bin/sail"

# start the application in background
$SAIL_PATH up -d

#sail artisan clear-compiled
$SAIL_PATH artisan view:clear
$SAIL_PATH artisan config:clear
$SAIL_PATH artisan config:cache
$SAIL_PATH artisan queue:restart

