<?php
/**
 * Command line script for executing PHPCS during a Travis build.
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *             
 * You need a robo.yml file with the ignored folders
 */


// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', getcwd());

require 'vendor/autoload.php';
use Symfony\Component\Yaml\Parser;


// Require Composer autoloader
if (!file_exists(REPO_BASE . '/vendor/autoload.php'))
{
	fwrite(STDOUT, "\033[37;41mThis script requires Composer to be set up, please run 'composer install' first.\033[0m\n");
}

require REPO_BASE . '/vendor/autoload.php';

// Reading config file
$yaml = new Parser();

try {
    $configuration = $yaml->parse(file_get_contents(REPO_BASE . '/travis-checks.yml'));
} catch (ParseException $e) {
    printf("Unable to parse the robo.yml : %s", $e->getMessage());
}

// Included folders to review
$include = array();

foreach ($configuration['check-code-style']['include'] as $includedFolder)
{
    $include[] = REPO_BASE . '/' . $includedFolder;
}

// Ignored files
$ignored = array();
foreach ($configuration['check-code-style']['ignore'] as $ignoredFiles)
{
    $ignored[] = REPO_BASE .  '/' . $ignoredFiles;
}


// Build the options for the sniffer
$options = array(
	'files'        => $include,
	'standard'     => array(REPO_BASE . '/travis/cli/phpcs/Joomla'),
	'ignored'      => $ignored,
	'showProgress' => true,
	'verbosity'    => false
);

fwrite(STDOUT, "\033[33;1mInitializing PHP_CodeSniffer checks.\033[0m\n");

// Instantiate the sniffer
$phpcs = new PHP_CodeSniffer_CLI;

// Ensure PHPCS can run, will exit if requirements aren't met
$phpcs->checkRequirements();

// Run the sniffs
$numErrors = $phpcs->process($options);

// If there were errors, output the number and exit the app with a fail code
if ($numErrors)
{
	fwrite(STDOUT, sprintf("\033[37;41mThere were %d issues detected.\033[0m\n", $numErrors));

	exit(1);
}
else
{
	fwrite(STDOUT, "\033[32;1mThere were no issues detected.\033[0m\n");
	exit(0);
}
