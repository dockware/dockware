#!/bin/bash

if [[ $IMG == "flex" ]]; then
  make run-flex url=http://localhost
elif [[ $IMG == "essentials" ]]; then
  make run-essentials url=http://localhost
elif [[ $IMG == "play" && $TAG == "latest" ]]; then
  make run6 url=http://localhost shopware=6.4.20.0
elif [[ $IMG == "dev" && $TAG == "latest" ]]; then
  make run6 url=http://localhost shopware=6.4.20.0
elif [[ $IMG == "dev" && $TAG == "6.5.0.0-rc1" ]]; then
  make run6 url=http://localhost shopware=6.5.0.0
elif [[ $TAG == "6."* ]]; then
  make run6 url=http://localhost shopware=$TAG
elif [[ $TAG == "5."* ]]; then
  make run5 url=http://localhost shopware=$TAG
else
  echo "NO CYPRESS CONFIGURATION FOUND FOR IMAGE"
  exit 1
fi