<?php

$yml = 'name: Release dockware/##image##:*

#only manually
on:
  workflow_dispatch:

jobs:';


$template='
    build-##image##-##tag-name##:
      name: Release ##image##:##tag##
      runs-on: ubuntu-latest
      steps:
        - name: Clone Code
          uses: actions/checkout@v2
    
        - name: Install Dependencies
          run: make install
    
        - name: ORCA Generate
          run: make generate
    
        - name: Build
          run: make build image=##image## tag=##tag## -B
    
        - name: Tests
          run: make test image=##image## tag=##tag## -B
          
        - name: Login to Docker Hub
          uses: docker/login-action@v1
          with:
            username: ${{ secrets.DOCKERHUB_USERNAME }}
            password: ${{ secrets.DOCKERHUB_PASSWORD }}
    
        - name: Push to Docker Hub
          run: docker push dockware/##image##:##tag##';



if (count($argv) < 2) {
    throw new Exception('you have to provide the image name you want to build');
}


$image = $argv[1];
$tag = null;
$tags = [];
if (count($argv) === 3) {
    $tag = $argv[2];
    if (trim($tag) !== '') {
        $tags = [];
        $tags[] = $tag;
    }
}

if (count($tags) === 0) {
    $folder = scandir(__DIR__ . '/../dist/images/' . $image);

    foreach ($folder as $tag) {
        if ($tag === '.' || $tag === '..') {
            continue;
        }
        $tags[] = $tag;
    }
}


$template = str_replace('##image##', $image, $template);
$yml = str_replace('##image##', $image, $yml);

foreach ($tags as $tag) {
    $tagName = str_replace('.','-',$tag);
    $ymlPart = str_replace('##tag##', $tag, $template);
    $ymlPart = str_replace('##tag-name##', $tagName, $ymlPart);
    $yml .= "\n  ".$ymlPart;
}

file_put_contents(__DIR__ . '/../.github/workflows/build_' . $image . '_docker-images.yml', $yml);