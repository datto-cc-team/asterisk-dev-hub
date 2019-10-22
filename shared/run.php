#!/usr/bin/php
<?php

// if already running, exit
$isRunning = intval(exec('ps aux | grep run.php | grep -v grep | wc -l'));
if ($isRunning > 1) {
    exit;
}

/**
 * start asterisk
 *
 * odbc connection was not starting (despite setting "depends on" in docker-compose to force the db to start first)
 * adding a sleep of 5 seconds before starting asterisk solved this issue
 *
 * Sometime starting asterisk with service results in error "Starting Asterisk PBX: Unable to install capabilities."
 * Using command asterisk starts without errors.  If asterisk was already running, a warning is displayed.
 */
sleep(5);
exec('asterisk');

/**
 * watch files and dirs for changes
 * sync changes to the container and take post move actions
 * inotify does not work correctly with docker volumes, hence using rsync
 */
echo 'starting watch loop' . PHP_EOL;
while (true) {

    // default
    $reloadAsterisk = false;
    $sharedConfigChange = false;

    // rsync agi scripts to the Asterisk AGI directory configured in asterisk.conf (aka "astagidir")
    // https://wiki.asterisk.org/wiki/display/AST/Directory+and+File+Structure
    $rsync = 'rsync -aEim --delete /shared-configs/agi/ /usr/share/asterisk/agi-bin/';
    $result = exec($rsync);
    if (!empty($result)) {
        echo 'agi-bin/ changed' . PHP_EOL;
    }

    // ssl dir
    $rsync = 'rsync -aEim --delete /shared-configs/ssl/ /etc/asterisk/ssl/';
    $result = exec($rsync);
    if (!empty($result)) {
        echo 'ssl config change' . PHP_EOL;
        $reloadAsterisk = true;
    }

    // watch for changes in shared asterisk-configs dir (excluding the ssl dir)
    $rsync = 'rsync -aEim --delete /shared-configs/asterisk-configs/ /tmp/shared-asterisk-configs/';
    $result = exec($rsync);
    if (!empty($result)) {
        echo 'shared asterisk configs changed';

        // if something changed in the shared configs, rsync files to /etc/asterisk/
        exec("rsync -aEim --delete --exclude 'ssl' /tmp/shared-asterisk-configs/ /etc/asterisk/");

        // set boolean so we override shared configs with instance configs
        $sharedConfigChange = true;

        $reloadAsterisk = true;
    }

    // check if instance specific changes need to be made.
    $rsync = 'rsync -aEim --delete /instance-configs/asterisk-configs/ /tmp/instance-asterisk-configs/';
    $result = exec($rsync);
    if ($sharedConfigChange || !empty($result)) {
        echo 'overwrite instance specific asterisk configs to /etc/asterisk/' . PHP_EOL;

        // overwrite shared configs with instance specific configs
        exec('exec cp -r /tmp/instance-asterisk-configs/ /etc/asterisk/');

        $reloadAsterisk = true;
    }

    // check if we need to reload asterisk
    if ($reloadAsterisk) {
        echo 'asterisk config change, reload asterisk' . PHP_EOL;
        exec('/usr/sbin/asterisk -rx reload');
    }

    sleep(2);
}
