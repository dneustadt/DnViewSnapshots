#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf DnViewSnapshots DnViewSnapshots-*.zip

# Build new release
mkdir -p DnViewSnapshots
git archive ${commit} | tar -x -C DnViewSnapshots
composer install --no-dev -n -o -d DnViewSnapshots
zip -r DnViewSnapshots-${commit}.zip DnViewSnapshots