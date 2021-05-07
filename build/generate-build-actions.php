<?php

$yml = 'name: Release dockware/##image##:*

#only manually
on:
  workflow_dispatch:

jobs:';


$template = '
    build-##image##-##tag-name##:
      name: Release ##image##:##tag##
      runs-on: ubuntu-latest
      steps:
        - name: Clone Code
          uses: actions/checkout@v2
    
        - name: Install Dependencies
          run: make install
    
        - name: ORCA Generate
          run: make generate -B
    
        - name: Build Image
          run: make build image=##image## tag=##tag## -B
    
        - name: Run SVRUnit Tests
          run: make test image=##image## tag=##tag## -B
          
        - name: Store SVRUnit Report
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: svrunit_report
            retention-days: 3
            path: |
                .reports
';


$templateCypress = '
        - name: Start Image
          run: docker run --rm -p 80:80 --name shop -d dockware/##image##:##tag##

        - name: Wait for Container
          uses: jakejarvis/wait-action@master
          with:
            time: "30s"

        - name: Container Output
          run: docker logs shop

        - name: Install Cypress
          run: cd tests/cypress && make install -B

        - name: Run Cypress Tests
          run: |
            if [[ $DW_IMAGE == flex ]]; then
               cd tests/cypress && make run-flex url=http://localhost
            fi
            if [[ $DW_IMAGE == essentials ]]; then
               cd tests/cypress && make run-essentials url=http://localhost
            fi
            if [[ $DW_IMAGE == play && $SW_VERSION == latest ]]; then
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $DW_IMAGE == dev && $SW_VERSION == latest ]]; then
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $SW_VERSION == 6.* ]]; then
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $SW_VERSION == 5.* ]]; then
               cd tests/cypress && make run5 url=http://localhost
            fi
          env:
            DW_IMAGE: ##image##
            SW_VERSION: ##tag##

        - name: Store Cypress Results
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: cypress_results_##image##_##tag##
            retention-days: 1
            path: |
              Tests/Cypress/cypress/videos
              Tests/Cypress/cypress/screenshots
';

$templatePush = '

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


$yml = str_replace('##image##', $image, $yml);
$template = str_replace('##image##', $image, $template);
$templateCypress = str_replace('##image##', $image, $templateCypress);
$templatePush = str_replace('##image##', $image, $templatePush);


foreach ($tags as $tag) {

    $tagName = str_replace('.', '-', $tag);


    # now build our template!
    # only add cypress tests for shopware images like play and dev
    $tagTemplate = $template;

    if ($image === 'play' || $image === 'dev' || $image === 'flex') {
        $tagTemplate .= $templateCypress;
    }

    $tagTemplate .= $templatePush;


    $ymlPart = str_replace('##tag##', $tag, $tagTemplate);
    $ymlPart = str_replace('##tag-name##', $tagName, $ymlPart);

    $yml .= "\n  " . $ymlPart;
}

file_put_contents(
    __DIR__ . '/../.github/workflows/build_' . $image . '_docker-images.yml',
    $yml
);
