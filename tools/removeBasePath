#!/bin/bash
#
# Replace the given basePath from the CodeCoverage reports
#
BASE_PATH=$1
PWD="`pwd`";
for i in `grep -r $BASE_PATH $PWD | cut -d ":" -f 1`; do 
    sed -i -E "s#$BASE_PATH##g" $i; 
done;