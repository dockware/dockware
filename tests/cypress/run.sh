#!/bin/bash

# remove -rcX suffix if present
TAG_CLEANED=${TAG%%-rc*}

if [[ $TAG_CLEANED == 6.7.* ]]; then
  echo "Skip tests for 6.7.x"
  exit 0
fi

if [[ $IMG == "flex" ]]; then
  make run-flex url=$URL
elif [[ $IMG == "essentials" ]]; then
  make run-essentials url=$URL
elif [[ $IMG == "play" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.6.0.0
elif [[ $IMG == "dev" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.6.0.0
elif [[ $TAG_CLEANED == 6.* ]]; then
  make run6 url=$URL shopware=$TAG_CLEANED
elif [[ $TAG_CLEANED == 5.* ]]; then
  make run5 url=http://localhost shopware=$TAG_CLEANED
else
  echo "NO CYPRESS CONFIGURATION FOUND FOR IMAGE"
  exit 1
fi