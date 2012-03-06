#!/bin/bash
#
# Replace the regex "@version(\s+)[\d\.]+" with "@version\1$newVersionString"
if [ $# -ne 1 ]; then
    echo "Usage: $0 <newVersionString>"
    exit
fi

newVersionString=$1;

for i in `grep -R "@version" src tests build.xml README LICENSE CHANGELOG| cut -d ":" -f 1` ; do
    sed -i "" -E "s/@version( +).*$/@version\1$newVersionString/g" $i
done