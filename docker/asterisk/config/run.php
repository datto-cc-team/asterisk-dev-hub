#!/usr/bin/php
<?php

// if already running, exit
$isRunning = intval(exec('ps aux | grep run.php | grep -v grep | wc -l'));
if ($isRunning > 1) {
    exit;
}

// copy sshd_config to /etc/ssh/sshd_config (-f = force)
exec('cp -f /phopo-config/sshd_config /etc/ssh/sshd_config');
// edit copy of sshd_config to use PHONE_PORTAL_SSH_PORT environment variable (-i = in place)
exec('sed -i "s/@@ASTERISK_SSH_PORT@@/$ASTERISK_SSH_PORT/g" /etc/ssh/sshd_config');

// start sshd
exec('service ssh stop');
exec('service ssh start');

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
//exec('service asterisk stop');
//exec('service asterisk start');

/**
 * watch files and dirs for changes
 * sync changes to the container and take post move actions
 * inotify does not work correctly with docker volumes, hence using rsync
 */
echo 'starting watch loop' . PHP_EOL;
while (true) {
    // watch for changes in asterisk-configs dir (excluding the ssl dir)
    $rsync = "rsync -aEim --delete --exclude 'ssl' /asterisk-configs/ /etc/asterisk/";
    $result = exec($rsync);
    if (!empty($result)) {
        echo 'asterisk config change, reload asterisk' . PHP_EOL;
        exec('/usr/sbin/asterisk -rx reload');
    }

    sleep(2);
}
