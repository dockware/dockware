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
            $php56 = false;

        } else if (version_compare($tag, '6.0') >= 0) {
            # SHOPWARE 6
            $php56 = false;

        } else if (version_compare($tag, '5.7') >= 0) {
            # SHOPWARE >= 5.7
            $php56 = true;
            $defaultPHP = '5';

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

        $xml .= '
        <testsuite name="Basic Checks" dockerImage="' . $imageFull . '">
            <directory>./../../tests/images/dev</directory>
            <directory>./../../tests/shared/base</directory>
            <directory>./../../tests/shared/dev</directory>
            <directory>./../../tests/packages/composer/v' . $composer . '</directory>
            <directory>./../../tests/packages/node/v' . $node . '</directory>
            <directory>./../../tests/packages/php/php' . $defaultPHP . '</directory>
        </testsuite>
         ';

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php81) {
            $xml .= '
        <testsuite name="PHP 8.1, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=8.1,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php8.1</directory>
            <directory>./../../tests /packages/xdebug/xdebug3</directory>
        </testsuite> 
        ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php8) {
            $xml .= '
        <testsuite name="PHP 8.0, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=8.0,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php8 </directory>
            <directory>./../../tests/packages/xdebug/xdebug3</directory>
        </testsuite> 
        ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php74) {
            $xml .= '
        <testsuite name="PHP 7.4, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=7.4,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php7</directory>
            <directory>./../../tests/packages/xdebug/xdebug3</directory>
            <directory>./../../tests/packages/sodium</directory>
        </testsuite>
         ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php73) {
            $xml .= '
        <testsuite name="PHP 7.3, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=7.3,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php7</directory>
            <directory>./../../tests/packages/xdebug/xdebug2</directory>
        </testsuite>
         ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php72) {
            $xml .= '
        <testsuite name="PHP 7.2, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=7.2,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php7</directory>
            <directory>./../../tests/packages/xdebug/xdebug2</directory>
        </testsuite> 
        ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php71) {
            $xml .= '
        <testsuite name="PHP 7.1, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=7.1,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php7</directory>
            <directory>./../../tests/packages/xdebug/xdebug2</directory>
        </testsuite>
         ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php7) {
            $xml .= '
        <testsuite name="PHP 7.0, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=7.0,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php7</directory>
            <directory>./../../tests/packages/xdebug/xdebug2</directory>
        </testsuite>
         ';

        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php56) {
            $xml .= '
        <testsuite name="PHP 5.6, XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=5.6,XDEBUG_ENABLED=1" >
            <directory>./../../tests/packages/php/php5</directory>
            <directory>./../../tests/packages/xdebug/xdebug2</directory>
        </testsuite> 
        ';
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        $xml .= '
    </testsuites >
</svrunit >
    ';

        return $xml;
    }

}
