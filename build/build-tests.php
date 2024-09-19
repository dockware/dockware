<?php

include __DIR__ . '/WorkflowBuilder/SVRUnitBuilder.php';


buildTests();


/**
 *
 */
function buildTests()
{
    $builder = new SVRUnitBuilder();

    $manifest = file_get_contents(__DIR__ . '/../manifest.json');
    $manifestJson = json_decode($manifest, true);

    foreach ($manifestJson['images'] as $image => $variants) {

        foreach ($variants as $variant) {

            $tag = explode(':', $variant)[1];

            $yml = $builder->buildJob(
                $image,
                $tag);

            if (empty($yml)) {
                continue;
            }

            file_put_contents(
                __DIR__ . '/../tests/svrunit/suites/' . $image . '/' . $tag . '.xml',
                $yml
            );

        }


    }
}