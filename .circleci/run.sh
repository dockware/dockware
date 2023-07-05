#!/bin/bash

# export to temporary file to debug values
cat <<EOT >> body.json
{
  "parameters":
      {
          "shopwareVersion": "$(SW_VERSION)",
          "setLatest" : $(SET_LATEST)
      }
}
EOT

cat body.json

curl -X POST -d @body.json -H "Content-Type: application/json" -H "Circle-Token: $CIRCLECI_KEY" https://circleci.com/api/v2/project/github/dockware/dockware/pipeline