#!/bin/bash

if [[ $IMG == "flex" ]]; then
  make run-flex url=$URL
elif [[ $IMG == "essentials" ]]; then
  make run-essentials url=$URL
elif [[ $IMG == "play" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.4.20.0
elif [[ $IMG == "dev" && $TAG == "latest" ]]; then
  make run6 url=$URL shopware=6.4.20.0
elif [[ $IMG == "dev" && $TAG == "6.5.0.0-rc1" ]]; then
  make run6 url=$URL shopware=6.5.0.0
elif [[ $TAG == "6."* ]]; then
  make run6 url=$URL shopware=$TAG
elif [[ $TAG == "5."* ]]; then
  make run5 url=$URL shopware=$TAG
else
  echo "NO CYPRESS CONFIGURATION FOUND FOR IMAGE"
  exit 1
fi