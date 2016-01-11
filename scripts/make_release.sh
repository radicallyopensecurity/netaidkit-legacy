#!/bin/bash
RELEASE_FILE=files/etc/nak-release
TIMESTAMP_FILE=files/etc/nak-timestamp

TAGS=$(git describe --tags)
echo "$TAGS" > $RELEASE_FILE

TIMESTAMP=$(date +%s)
BRANCH=$(git symbolic-ref --short HEAD)
echo "$TIMESTAMP-$BRANCH" > $TIMESTAMP_FILE

echo "$TAGS"
