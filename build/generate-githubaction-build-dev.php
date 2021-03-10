<?php

$yml = 'name: Release dockware/##image##:*

#only manuelly
on:
  workflow_dispatch:

jobs:';


$template='
    build-##image##-##tag-name##:
      name: build dockware/##image##:##tag##
      runs-on: ubuntu-latest
      steps:
        - name: Checkout code
          uses: actions/checkout@v2
    
        - name: install
          run: make install
    
        - name: Generate docker files
          run: make generate
    
        - name: Generate Build commands for dev image
          run: php ./build/build.php ##image## ##tag##
    
        - name: Build the ##image## ##tag## image
          run: make build image=##image## tag=##tag##
    
        - name: Login to Docker Hub
          uses: docker/login-action@v1
          with:
            username: ${{ secrets.DOCKERHUB_USERNAME }}
            password: ${{ secrets.DOCKERHUB_PASSWORD }}
    
        - name: Push Images to dockerhub
          run: php ./build/push_dockerhub.php ##image## ##tag##';


include(__DIR__ . '/_variables.inc.php');

$template = str_replace('##image##', $image, $template);
$yml = str_replace('##image##', $image, $yml);

foreach ($tags as $tag) {
    $tagName = str_replace('.','-',$tag);
    $ymlPart = str_replace('##tag##', $tag, $template);
    $ymlPart = str_replace('##tag-name##', $tagName, $ymlPart);
    $yml .= "\n  ".$ymlPart;
}

file_put_contents(__DIR__ . '/../.github/workflows/build_' . $image . '_docker-images.yml', $yml);