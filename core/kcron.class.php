<?

//--------------------------------------------------------------------------------------------------
//*	global cron object
//--------------------------------------------------------------------------------------------------
//+	This object should be initialized and run at 10 minute intervals by calling:
//+
//+	(serverPath)admin/cron/
//+
//+	On linux, the following crontab entry should accomplish this:
//+
//+	5,15,25,35,45,55 * * * * root wget --output-document="/dev/null" "http://mysite.tld/admin/cron/"
//+
//+	Windows users should set up a Scheduled task
//+
//+	Note that daily jobs are only run sometime after 3am local time (usually a slack period)
//+	TODO: consider adding 'weekly' and 'monthly' options for log rotation

class KCron {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName = 'data/lastrun.cron.php';	//_	location of lastrun file [string]
	var $tenmins = 0;							//_	timestamp when last tenmins jobs were run [int]
	var $hourly = 0;							//_	timestamp when last hourly jobs were run [int]
	var $daily = 0;								//_	timestamp when last daily jobs were run [int]
	var $loaded = false;						//_	true when lastrun.cron.php file is loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KCron() {
		$this->load();		
	}	

	//----------------------------------------------------------------------------------------------
	//.	load the last run tomes from the registry
	//----------------------------------------------------------------------------------------------
	//returns: true on success, fals on failure [bool]

	function load() {
		global $kapenta, $registry;
		$this->tenmins = (int)$registry->get('kapenta.cron.tenmins');
		$this->hourly = (int)$registry->get('kapenta.cron.hourly');
		$this->daily = (int)$registry->get('kapenta.cron.daily');
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save to registry
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta, $registry, $db;
		$registry->set('kapenta.cron.tenmins', $this->tenmins);
		$registry->set('kapenta.cron.hourly', $this->hourly);
		$registry->set('kapenta.cron.daily', $this->daily);
	}

	//----------------------------------------------------------------------------------------------
	//.	run any tasks which are now due
	//----------------------------------------------------------------------------------------------
	//returns: HTML report of actions taken [string]

	function run() {
		global $db, $session;
		$report = "<h1>" . $db->datetime() . "</h1>\n";		//%	return value [string]
		$now = time();										//%	timestamp when script started [int]

		$msg = "running cron: $now #" . $db->datetime($now) . "<br/>\n"
			 . "tenmins: " . $this->tenmins . " #" . $db->datetime($this->tenmins) . "<br/>\n"
			 . "hourly: " . $this->hourly . " #" . $db->datetime($this->hourly) . "<br/>\n"
			 . "daily: " . $this->daily . " #" . $db->datetime($this->daily) . "<br/>\n";

		$session->msgAdmin($msg);

		if (($now - $this->tenmins) > 600) { 
			$report .= $this->runTasks('tenmins'); 
			$this->tenmins = $now;
			$this->save();
		}

		if (($now - $this->hourly) > 3600) { 
			$report .= $this->runTasks('hourly');
			$this->hourly = $now;
			$this->save(); 
		}

		if (($now - $this->daily) > 86400) {
			$hour = (int)date('G', $now);
			if (($hour >= 3) && ($hour < 5)) {				// only between 3 and 4 in the morning
				$report .= $this->runTasks('daily');		// TODO: make this ^^^ configurable 
				$this->daily = $now;						
				$this->save();
			}
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	run any tasks for a particular interval
	//----------------------------------------------------------------------------------------------
	//arg: interval - name of interval (tenmins|hourly|weekly) [string]

	function runTasks($interval) {
		global $kapenta, $db, $user, $theme, $page, $session;
		$report = '';

		$session->msgAdmin('running task set: ' . $interval);

		$mods = $kapenta->listModules();
		foreach($mods as $modName) {
			$incFile = 'modules/' . $modName . '/inc/cron.inc.php';
			if (true == $kapenta->fileExists($incFile)) {
				require_once($kapenta->installPath . $incFile);
				$fnName = $modName . '_cron_' . $interval;
				if (true == function_exists($fnName)) { 
					$session->msgAdmin('running task set: ' . $incFile . ' (' . $interval . ')');
					$report .= $fnName();
				}
			}
		}
		return $report;
	}

}

?>
