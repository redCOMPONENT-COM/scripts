<?php
/**
 * Simple application that list the latest Pull requests on a specific repository
 */

namespace Changeloger;

// Maximise error reporting.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';


use Joomla\Application\AbstractCliApplication;
use Joomla\Github\Github;
use Joomla\Registry\Registry;

/**
 * Class Changeloger
 *
 * Simple Command Line Application to get the latest Joomla for running the tests
 */
class ChangelogPulls extends AbstractCliApplication
{
	/**
	 * The application version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const VERSION = '1.0';

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		$this->out('changeloger v' . self::VERSION);
		$options = new Registry;

		// Get your token at https://github.com/settings/applications#personal-access-tokens
		$options->set('gh.token', 'af3cb3e9715c0a1f1b229173d144c53dcca99a40');

		$github = new Github($options);

		$pulls = $github->pulls->getList('redCOMPONENT-COM', 'redSHOP', 'closed', 0, 300);


		foreach ($pulls as $pull)
		{
			$pullSummary[] = '+ ' . $pull->title;
		}

		$summary = implode("\n", $pullSummary);


		$this->out($summary);

		$commits = $github->pulls->create('redCOMPONENT-COM', 'redSHOP', 'v1.5', 'v1.5.0.1');

		foreach ($commits as $commit)
		{
			$commitSummary[] = '+ ' . $commit->title;
		}

		$summary = implode("\n", $commitSummary);


		$this->out($summary);
	}
}

define('JPATH_ROOT', realpath(dirname(__DIR__)));

$app = new ChangelogPulls;
$app->execute();

