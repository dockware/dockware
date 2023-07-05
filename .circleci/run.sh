#!/bin/bash

# export to temporary file to debug values
cat <<EOT >> body.json
{
  "parameters":
      {
          "shopwareVersion": "$2",
          "setLatest" : $3
      }
}
EOT

cat body.json

curl -X POST -d @body.json -H "Content-Type: application/json" -H "Circle-Token: $1" https://circleci.com/api/v2/project/github/dockware/dockware/pipeline