#!/usr/bin/env php
<?php

/*
 * This file is part of the Wordpress On Dotcloud package.
 *
 * (c) Quentin Pleplé <quentin.pleple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define("ENVIRONMENT_FILE_NAME", dirname(__FILE__) . '/../../environment.json');
define("WP_CONFIG_FILE_NAME", dirname(__FILE__) . '/../wp-config.php');
define("WP_CONFIG_sample_FILE_NAME", dirname(__FILE__) . '/../wp-config-sample.php');
// the name of the database that will be created for wordpress
define("DB_NAME", "wordpress"); 
define("MSG_PREFIX", "[wordpress-on-dotcloud] �");
/**********************************
  Reading environment variables
 **********************************/
if (!file_exists(ENVIRONMENT_FILE_NAME)) {
    die(MSG_PREFIX . "Error: File environment.json does not exists. Looking at: " . ENVIRONMENT_FILE_NAME . "\n");
}
$json = @file_get_contents(ENVIRONMENT_FILE_NAME);
if (empty($json)) {
    die(MSG_PREFIX . "Error: Can't read environment.json file.\n");
}
echo MSG_PREFIX . "File environment.json found and read\n";

$environment = @json_decode($json);
if (empty($environment)) {
    die(MSG_PREFIX . "Error: Content of environment.json is not valid json.\n");
}

$properties = array("DOTCLOUD_DB_MYSQL_LOGIN", "DOTCLOUD_DB_MYSQL_PASSWORD", "DOTCLOUD_DB_MYSQL_HOST", "DOTCLOUD_DB_MYSQL_PORT");
$errorStr = "";
foreach ($properties as $property) {
    if (!property_exists($environment, $property)) {
        $errorStr .= MSG_PREFIX . "Error: Missing property $property in file environment.json\n";
    }
}
if ($errorStr != "") {
    die($errorStr);
}
echo MSG_PREFIX . "File environment.json parsed\n";

/**********************************
  Opening wp-config.php 
 **********************************/
if (file_exists(WP_CONFIG_FILE_NAME)) {
    $content = @file_get_contents(WP_CONFIG_FILE_NAME);
    if (empty($content)) {
        echo MSG_PREFIX . "File wp-config.php not found (looking at: " . WP_CONFIG_FILE_NAME . "). Trying to find wp-config-sample.php\n";
    }
    echo MSG_PREFIX . "File wp-config.php found and read\n";
} else {
    echo MSG_PREFIX . "File wp-config.php empty (looking at: " . WP_CONFIG_FILE_NAME . "). Trying to find wp-config-sample.php\n";
}

if (empty($content)) {
    if (!file_exists(WP_CONFIG_sample_FILE_NAME)) {
        die(MSG_PREFIX . "Error: File wp-config-sample.php not found. Looking at: " . WP_CONFIG_sample_FILE_NAME . "\n");
    }
    
    $content = @file_get_contents(WP_CONFIG_sample_FILE_NAME);
    if (empty($content)) {
        die(MSG_PREFIX . "Error: Can't read wp-config-sample.php file.\n");
    }
    echo MSG_PREFIX . "File wp-config-sample.php found and read\n";
}

/**********************************
  Replacing config values
 **********************************/
$configValues = array(
    "DB_NAME" => DB_NAME,
    "DB_USER" => $environment->DOTCLOUD_DB_MYSQL_LOGIN,
    "DB_PASSWORD" => $environment->DOTCLOUD_DB_MYSQL_PASSWORD,
    "DB_HOST" => $environment->DOTCLOUD_DB_MYSQL_HOST . ":" . $environment->DOTCLOUD_DB_MYSQL_PORT
);

foreach ($configValues as $property => $value) {
    echo MSG_PREFIX . "Setting $property = $value\n";
    $count = 0;
    $content = preg_replace('/(define\(\'' . $property . '\', \')(.*)(\'\);)/', '${1}' . $value . '${3}', $content, -1, &$count);
    if ($count == 0) {
        die(MSG_PREFIX . "Error: Property $property not found in wp-config.\n");
    }
}

/**********************************
  Writing wp-config.php
 **********************************/
echo MSG_PREFIX . "Saving modifications\n";
$handler = fopen(WP_CONFIG_FILE_NAME, 'w') or die(MSG_PREFIX . "Error: can't open file wp-config.php to save changes.\n");
fwrite($handler, $content);
fclose($handler);
echo MSG_PREFIX . "Modifications saved.\n";


/**********************************
  Creating DB if not exists
 **********************************/
echo MSG_PREFIX . "Creating database '" . DB_NAME . "' if not exists\n";
$mysqli = new mysqli(
    $environment->DOTCLOUD_DB_MYSQL_HOST,
    $environment->DOTCLOUD_DB_MYSQL_LOGIN,
    $environment->DOTCLOUD_DB_MYSQL_PASSWORD,
    "",
    $environment->DOTCLOUD_DB_MYSQL_PORT
);

$mysqli->query('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ';') or die(MSG_PREFIX . "Error while creating database " . DB_NAME . "\n");

echo MSG_PREFIX . "Database created (if did not exist already)\n";
echo MSG_PREFIX . "Ready to blog!\n";
