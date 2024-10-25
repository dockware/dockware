<?php

class SVRUnitBuilder
{
    /**
     * @param $image
     * @param $tag
     * @return string
     */
    public function buildJob($image, $tag)
    {
        if ($image !== 'play' && $image !== 'dev') {
            return '';
        }

        $imageFull = 'dockware/' . $image . ':' . $tag;
        $isDev = ($image === 'dev');

        $sodium = true;
        $node = '12';
        $composer = '2';

        $ubuntuVersion = '22';

        if ($this->isVersionGTE($tag, '6.5.8.8')) {
            # SHOPWARE >= 6.5.8.8
            $defaultPHP = '8.3';


            $php83 = true;
            $php82 = true;

            $php81 = false;
            $php8 = false;
            $php74 = false;
            $php73 = false;
            $php72 = false;
            $php71 = false;
            $php7 = false;
            $php56 = false;

            $node = '20';

        } else if ($tag === 'latest' || $this->isVersionGTE($tag, '6.5')) {
            # SHOPWARE >= 6.5
            $defaultPHP = '8';

            $php83 = true;
            $php82 = true;
            $php81 = true;

            $php8 = false;
            $php74 = false;
            $php73 = false;
            $php72 = false;
            $php71 = false;
            $php7 = false;
            $php56 = false;

            $node = '18';

        } else if ($this->isVersionGTE($tag, '6.0')) {
            # SHOPWARE >= 6.0
            $defaultPHP = '8';

            $php83 = true;
            $php82 = true;
            $php81 = true;
            $php8 = true;
            $php74 = true;

            $php73 = false;
            $php72 = false;
            $php71 = false;
            $php7 = false;
            $php56 = false;

        } else if ($this->isVersionGTE($tag, '5.7')) {
            # SHOPWARE >= 5.7
            $defaultPHP = '8';

            $php83 = true;
            $php82 = true;
            $php81 = true;
            $php8 = true;
            $php74 = true;
            $php73 = true;
            $php72 = true;

            $php71 = false;
            $php7 = false;
            $php56 = false;

            $ubuntuVersion = '22';

        } else {
            # SHOPWARE < 5.7
            $composer = '1';
            $defaultPHP = '5';

            $php83 = false;
            $php82 = false;
            $php81 = false;
            $php8 = false;
            $php74 = false;
            $php73 = false;
            $php72 = false;
            $php71 = false;

            $php7 = true;
            $php56 = true;

            $sodium = false;

            $ubuntuVersion = '18';
        }


        $xml = '<svrunit setupTime="30">
    <testsuites>
    ';

        $xml .= '
        <testsuite name="' . $imageFull . ', Command Runner" group="command-runner" dockerImage="' . $imageFull . '" dockerCommandRunner="true">' . PHP_EOL;
        if ($isDev) {
            $xml .= '            <directory>./../../tests/shared/command-runner/dev</directory>' . PHP_EOL;
        } else {
            $xml .= '            <directory>./../../tests/shared/command-runner/play</directory>' . PHP_EOL;
        }
        $xml .= '        </testsuite>
        ';

        # -------------------------------------------------------------------------------------------------------------------------------

        $devPart = '
            <directory>./../../tests/images/dev</directory>
            <directory>./../../tests/shared/dev</directory>
            <directory>./../../tests/packages/node/v' . $node . '</directory>
            <directory>./../../tests/packages/composer/v' . $composer . '</directory>';

        $sharedBaseSW = './../../tests/shared/base-6.0';
        $shopwareCLI = '';


        if ($this->isVersionGTE($tag, '6.5')) {
            $sharedBaseSW = './../../tests/shared/base-6.5';
        }

        if (!$isDev) {
            $devPart = '';
        }

        if ($isDev && $this->isVersionGTE($tag, '6.0')) {
            $shopwareCLI = '
            <directory>./../../tests/packages/shopware-cli/</directory>';
        }

        $ubuntu = './../../tests/packages/ubuntu/' . $ubuntuVersion;

        $xml .= '
        <testsuite name="' . $imageFull . ', Core Checks" group="core" dockerImage="' . $imageFull . '">
            <directory>./../../tests/shared/base</directory>
            <directory>"' . $ubuntu . '"</directory>
            <directory > ' . $sharedBaseSW . '</directory> ' . $devPart . $shopwareCLI . '
            <directory >./../../tests/packages/php/php' . $defaultPHP . ' </directory>
        </testsuite >
    ';

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($isDev) {
            $xml .= '
        <testsuite name="' . $imageFull . ', ENV Node Version Switch" dockerImage="' . $imageFull . '" dockerEnv="NODE_VERSION=' . $node . '">
            <directory>./../../tests/packages/node/v' . $node . '</directory>
        </testsuite>
            ';
        }

        $xml .= '
        <testsuite name="' . $imageFull . ', Recovery Mode works" dockerImage="' . $imageFull . '" dockerEnv="RECOVERY_MODE=1">
            <directory>./../../tests/shared/recovery_mode</directory>
        </testsuite>
            ';

        # -------------------------------------------------------------------------------------------------------------------------------
        $testXdebugOff = $isDev; // only test for dev images where XDebug exists
        # and only the first one

        if ($php83) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '8.3', '8.3', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        if ($php82) {
            $xdebug = ($isDev) ? '3.2.0' : '';
            $xml .= $this->buildVersion($imageFull, '8.2', '8.2', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php81) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '8.1', '8.1', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php8) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '8.0', '8', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php74) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '7.4', '7', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php73) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '7.3', '7', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php72) {
            $xdebug = ($isDev) ? '3' : '';
            $xml .= $this->buildVersion($imageFull, '7.2', '7', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php71) {
            $xdebug = ($isDev) ? '2' : '';
            $xml .= $this->buildVersion($imageFull, '7.1', '7', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php7) {
            $xdebug = ($isDev) ? '2' : '';
            $xml .= $this->buildVersion($imageFull, '7.0', '7.0', $xdebug, $sodium, $testXdebugOff);
            $testXdebugOff = false;
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        if ($php56) {
            $xdebug = ($isDev) ? '2' : '';
            $xml .= $this->buildVersion($imageFull, '5.6', '5', $xdebug, $sodium, $testXdebugOff);
        }

        # -------------------------------------------------------------------------------------------------------------------------------

        $xml .= '
    </testsuites >
</svrunit >' . PHP_EOL;

        return $xml;
    }

    /**
     * @param $imageFull
     * @param $fullPHP
     * @param $php
     * @param $xDebug
     * @param $sodium
     * @param bool $testXdebugOff
     * @return string
     */
    private function buildVersion($imageFull, $fullPHP, $php, $xDebug, $sodium, bool $testXdebugOff)
    {
        $xml = '';

        if (!empty($xDebug)) {
            $xml = PHP_EOL;
            $xml .= '        <testsuite name="' . $imageFull . ', PHP ' . $fullPHP . ', XDebug ON" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=' . $fullPHP . ',XDEBUG_ENABLED=1">' . PHP_EOL;
            $xml .= '            <directory>./../../tests/packages/php/php' . $php . '</directory>' . PHP_EOL;
            $xml .= '            <directory>./../../tests/packages/xdebug/xdebug' . $xDebug . '</directory>' . PHP_EOL;
            if ($sodium) {
                $xml .= '            <directory>./../../tests/packages/sodium</directory>' . PHP_EOL;
            }
            $xml .= '        </testsuite>' . PHP_EOL;
        }

        if ($testXdebugOff) {
            //disabled xdebug
            $xml .= PHP_EOL;
            $xml .= '        <testsuite name="' . $imageFull . ', PHP ' . $fullPHP . ', XDebug OFF" dockerImage="' . $imageFull . '" dockerEnv="PHP_VERSION=' . $fullPHP . ',XDEBUG_ENABLED=0">' . PHP_EOL;
            $xml .= '            <directory>./../../tests/packages/php/php' . $php . '</directory>' . PHP_EOL;
            $xml .= '            <directory>./../../tests/packages/xdebug/xdebug-off</directory>' . PHP_EOL;
            $xml .= '        </testsuite>' . PHP_EOL;
        }

        return $xml;
    }


    /**
     * @param $currentVersion
     * @param $cmpVersion
     * @return bool
     */
    private function isVersionGTE($currentVersion, $cmpVersion)
    {
        $currentVersion = preg_replace('/-rc\d+$/', '', $currentVersion);

        return (version_compare($currentVersion, $cmpVersion, '>='));
    }

}
