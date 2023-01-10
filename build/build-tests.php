<?php
ini_set('DISPLAY_ERROR', 'E_ALL');
include __DIR__.'/TestPipelineBuilder.php';

buildTests();

/**
 *
 */
function buildTests()
{
    $builder = new TestPipelineBuilder();

    $manifest = file_get_contents(__DIR__.'/../manifest.json');
    $manifestJson = json_decode($manifest, true);

    foreach ($manifestJson['images'] as $image => $variants) {
        if (!is_dir(__DIR__.'/../tests/svrunit/suites/'.$image)) {
            mkdir(__DIR__.'/../tests/svrunit/suites/'.$image);
        }

        foreach ($variants as $variant) {
            $tag = explode(':', $variant)[1];
            $yml = $builder->buildJob(
                $image.'-'.str_replace('.', '-', $tag),
                $image,
                $tag
            );

            if (empty($yml)) {
                echo "\n not created for $image/$variant because there is no test content generated";
                continue;
            }

            file_put_contents(
                __DIR__.'/../tests/svrunit/suites/'.$image.'/'.$tag.'.xml',
                $yml
            );
        }
    }
}
