<?php

include __DIR__ . '/TestPipelineBuilder.php';


buildTests();


/**
 *
 */
function buildTests()
{
    $builder = new TestPipelineBuilder();

    $manifest = file_get_contents(__DIR__ . '/../manifest.json');
    $manifestJson = json_decode($manifest, true);

    foreach ($manifestJson['images'] as $image => $variants) {

        foreach ($variants as $variant) {

            $yml = '';

            $tag = explode(':', $variant)[1];

            $job = $builder->buildJob(
                $image . '-' . str_replace('.', '-', $tag),
                $image,
                $tag);

            $yml .= "\n  " . $job;

            file_put_contents(
                __DIR__ . '/../tests/svrunit/suites/' . $image . '/' . $tag . '.xml',
                $yml
            );

        }


    }
}