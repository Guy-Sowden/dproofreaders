#!/bin/bash

if [ "_$1" != "_" ]; then
    BASE_DIR=$1
elif [ -d SETUP ]; then
    BASE_DIR=.
elif [ -d ../SETUP ]; then
    BASE_DIR=../
else
    echo "Unable to determine base code directory"
    exit 1
fi

echo "Checking all .php and .inc files under $BASE_DIR for linting errors..."

for file in `find $BASE_DIR -name "*.php" -o -name "*.inc"`; do
    echo $file
    # We route stderr to /dev/null to avoid flooding the console with
    # deprecation errors about magic quotes. After that is resolved
    # we should remove the redirect of stderr.
    OUTPUT=`php -l $file 2>/dev/null`
    echo $OUTPUT | grep -q 'No syntax errors detected'
    if [ $? -ne 0 ]; then
        echo "Lint failure in $file"
        echo "$OUTPUT"
        exit 1
    fi
done
