#!/bin/bash

curl -X POST https://circleci.com/api/v2/project/github/dockware/dockware/pipeline \
  -H "Content-Type: application/json" \
  -H "Circle-Token: $CIRCLECI_KEY" \
  -d "{
        \"parameters\":
            {
                \"shopwareVersion\": \"$SW_VERSION\",
                \"setLatest\" : $SET_LATEST
            }
      }"

