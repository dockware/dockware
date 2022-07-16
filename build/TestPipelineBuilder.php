<?php


class TestPipelineBuilder
{

    /**
     * @param $jobKey
     * @param $image
     * @param $tag
     * @return string
     */
    public function buildJob($jobKey, $image, $tag)
    {
        if ($image !== 'play' && $image !== 'dev') {
            return;
        }

        $imageFull = 'dockware/' . $image . ':' . $tag;
        $isDev = ($image === 'dev');

        $defaultPHP = '8';
        $php81 = true;
        $php8 = true;
        $php74 = true;
        $php73 = true;
        $php72 = true;
        $php71 = true;
        $php7 = true;
        $php56 = true;

        $node = '12';
        $composer = '2';

        if ($tag === 'latest' || version_compare($tag, '6.0') >= 0) {
            # SHOPWARE 6
            $php73 = false;
            $php72 = false;
            $php71 = false;
            $php7 = false;
            $php56 = false;

        } else if (version_compare($tag, '6.0') >= 0) {
            # SHOPWARE 6
            $php56 = false;

        } else if (version_compare($tag, '5.7') >= 0) {
            # SHOPWARE >= 5.7
            $defaultPHP = '7';

            $php72 = true;
            $php71 = true;
            $php7 = true;
            $php56 = false;

        } else {
            # SHOPWARE < 5.7
            $composer = '1';

            $defaultPHP = '5';
            $php81 = false;
            $php8 = false;
            $php74 = true;
            $php73 = true;
            $php72 = true;
            $php56 = true;
        }

        $xml = '
<svrunit setupTime="30">
    <testsuites>
    ';

        $xml .= '
        <testsuite name="' . $imageFull . ', Command Runner" dockerImage="' . $imageFull . '" dockerCommandRunner="true">
            <directory>./../../tests/shared/command-runner</directory>
        </testsuite>
        ';

        # -------------------------------------------------------------------------------------------------------------------------------

        $devPart = '
            <directory>./../../tests/images/dev</directory>
            <directory>./../../tests/shared/dev</directory>
            <directory>./../../tests/packages/node/v' . $node . '</directory>';
        if (!$isDev) {
            $devPart = '';
        }

        $xml .= '
        <testsuite name="Basic Checks" dockerImage="' . $imageFull . '">
            <directory>./../../tests/shared/base</directory>' . $devPart . '
            <directory>./../../tests/packages/composer/v' . $composer . '</directory>
            <directory>./../../tests/packages/php/php' . $defaultPHP . '</directory>
        </testsuite>
         ';

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php81) {
            $xml .= $this->buildVersion($imageFull, '8.1', '8.1', '3');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php8) {
            $xml .= $this->buildVersion($imageFull, '8.0', '8', '3');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php74) {
            $xml .= $this->buildVersion($imageFull, '7.4', '7', '3');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php73) {
            $xml .= $this->buildVersion($imageFull, '7.3', '7', '2');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php72) {
            $xml .= $this->buildVersion($imageFull, '7.2', '7', '2');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php71) {
            $xml .= $this->buildVersion($imageFull, '7.1', '7', '2');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php7) {
            $xml .= $this->buildVersion($imageFull, '7.0', '7', '2');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php56) {
            $xml .= $this->buildVersion($imageFull, '5.6', '5', '2');
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        $xml .= '
    </testsuites >
</svrunit >
    ';

        return $xml;
    }

    /**
     * @param $imageFull
     * @param $php
     * @param $xDebug
     * @return string
     */
    private function buildVersion($imageFull, $fullPHP, $php, $xDebug)
    {
        $xml = PHP_EOL;
        $xml .= '       <testsuite name="PHP ' . $fullPHP . ', XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=' . $fullPHP . ',XDEBUG_ENABLED=1">' . PHP_EOL;
        $xml .= '            <directory>./../../tests/packages/php/php' . $php . '</directory>' . PHP_EOL;
        $xml .= '            <directory>./../../tests/packages/xdebug/xdebug' . $xDebug . '</directory>' . PHP_EOL;
        $xml .= '            <directory>./../../tests/packages/sodium</directory>' . PHP_EOL;
        $xml .= '       </testsuite>' . PHP_EOL;

        return $xml;
    }

}
