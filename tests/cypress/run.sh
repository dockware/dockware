#!/bin/bash

if [[ $IMG == "flex" || $IMG == "production" ]]; then
  make run-flex url=$URL
elif [[ $IMG == "base_play" || $IMG == "base_dev" ]]; then
  make run-flex url=$URL
elif [[ $IMG == "essentials" || $IMG == "essentials_play" ]]; then
  make run-essentials url=$URL
elif [[ $IMG == "play" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.5.0.0
elif [[ $IMG == "dev" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.5.0.0
elif [[ $TAG == "6."* ]]; then
  make run6 url=$URL shopware=$TAG
elif [[ $TAG == "5."* ]]; then
  make run5 url=http://localhost shopware=$TAG
else
  echo "NO CYPRESS CONFIGURATION FOUND FOR IMAGE"
  exit 1
fi