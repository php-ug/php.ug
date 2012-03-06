#!/bin/bash
#
# Replace the second year of the copyright note with the given Year
if [ $# -ne 1 ]; then
    echo "Usage: $0 <newCopyrightYear>"
    exit
fi

newYear=$1;

for i in `grep -R "@copyright" src tests build.xml README LICENSE CHANGELOG| cut -d ":" -f 1` ; do
    sed -i "" -E "s/@copyright([^-]+)-[^ ]+(.+)$/@copyright\1-$newYear\2/g" $i
done

for i in `grep -R "Copyright" src tests build.xml README LICENSE CHANGELOG| cut -d ":" -f 1` ; do
    sed -i "" -E "s/(Copyright \(c\) [^-]+)-[^ ]+(.+)$/\1-$newYear\2/g" $i
done