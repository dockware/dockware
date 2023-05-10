<?php

include __DIR__ . '/WorkflowBuilder/GithubWorkflowBuilder.php';


buildManifestImageJobs();
buildShopwareVersionJob();
buildCustomImageJob();


/**
 *
 */
function buildManifestImageJobs()
{
    $builder = new GithubWorkflowBuilder();

    $manifest = file_get_contents(__DIR__ . '/../manifest.json');
    $manifestJson = json_decode($manifest, true);

    foreach ($manifestJson['images'] as $image => $variants) {

        $yml = 'name: ' . $image . ' (All)

on:
  workflow_dispatch:

jobs:';

        foreach ($variants as $variant) {

            $tag = explode(':', $variant)[1];

            $job = $builder->buildJob(
                $image . '-' . str_replace('.', '-', $tag),
                $image,
                $tag);

            $yml .= "\n  " . $job;
        }

        file_put_contents(
            __DIR__ . '/../.github/workflows/' . $image . '.yml',
            $yml
        );

    }
}


/**
 *
 */
function buildShopwareVersionJob()
{
    $builder = new GithubWorkflowBuilder();

    $yml = 'name: Shopware Version
run-name: Shopware ${{ github.event.inputs.tagName }}

on:
  workflow_dispatch:
    inputs:
      tagName:
        description: \'Tag Name\'
        required: true
        
jobs:';

    $job = $builder->buildJob(
        'build-play',
        'play',
        '${{ github.event.inputs.tagName }}'
    );

    $yml .= "\n  " . $job;

    $job = $builder->buildJob(
        'build-dev',
        'dev',
        '${{ github.event.inputs.tagName }}'
    );

    $yml .= "\n  " . $job;

    file_put_contents(
        __DIR__ . '/../.github/workflows/shopware.yml',
        $yml
    );
}

/**
 *
 */
function buildCustomImageJob()
{
    $builder = new GithubWorkflowBuilder();

    $yml = 'name: Custom Image
run-name: Shopware ${{ github.event.inputs.tagName }}

on:
  workflow_dispatch:
    inputs:
      imageName:
        description: \'Image Name\'
        required: true
      tagName:
        description: \'Tag Name\'
        required: true
        
jobs:';

    $job = $builder->buildJob(
        'build',
        '${{ github.event.inputs.imageName }}',
        '${{ github.event.inputs.tagName }}'
    );

    $yml .= "\n  " . $job;

    file_put_contents(
        __DIR__ . '/../.github/workflows/image-build.yml',
        $yml
    );
}