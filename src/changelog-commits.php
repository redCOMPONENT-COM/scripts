<?php
/**
 * Simple application that lists the commits between two branches or versions
 */

namespace ChangelogerCommits;

// Maximise error reporting.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';


use Joomla\Application\AbstractCliApplication;

/**
 * Class Changeloger
 *
 * Simple Command Line Application to get the latest Joomla for running the tests
 */
class ChangelogerCommits extends AbstractCliApplication
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
		$this->out(getcwd());
		$this->out('changeloger v' . self::VERSION);
		$this->out();
		$branch1 = $this->input->get('previous', 'develop');
		$branch2 = $this->input->get('current');

		$commmand = 'git log --oneline ' . $branch1 . '...' . $branch2 . '--no-merges --format="* %s ( %h )"';

		exec($commmand, $output, $return);

		$summary = implode("\n", $output);

		$this->out($summary);
	}
}

$app = new ChangelogerCommits;
$app->execute();

