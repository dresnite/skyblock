<?php

$output = "SkyBlock.phar";

if(is_file($output)) {
    unlink($output);
}

$phar = new Phar($output);
$phar->startBuffering();
$phar->buildFromDirectory(__DIR__);
$phar->stopBuffering();

echo "SkyBlock phar file has been built";
