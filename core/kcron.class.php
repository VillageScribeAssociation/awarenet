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
	//.	load the last run times from the registry
	//----------------------------------------------------------------------------------------------
	//returns: true on success, fals on failure [bool]

	function load() {
		global $kapenta, $registry;
		$this->tenmins = (int)$registry->get('cron.tenmins');
		$this->hourly = (int)$registry->get('cron.hourly');
		$this->daily = (int)$registry->get('cron.daily');
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save to registry
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta;
		$kapenta->registry->set('cron.tenmins', $this->tenmins);
		$kapenta->registry->set('cron.hourly', $this->hourly);
		$kapenta->registry->set('cron.daily', $this->daily);
	}

	//----------------------------------------------------------------------------------------------
	//.	run any tasks which are now due
	//----------------------------------------------------------------------------------------------
	//returns: HTML report of actions taken [string]

	function run() {
		global $kapenta;

		$report = "<h1>" . $kapenta->db->datetime() . "</h1>\n";		//%	return value [string]
		$now = $kapenta->time();							//%	timestamp when script started [int]

		$msg = "running cron: $now #" . $kapenta->db->datetime($now) . "<br/>\n"
			 . "tenmins: " . $this->tenmins . " #" . $kapenta->db->datetime($this->tenmins) . "<br/>\n"
			 . "hourly: " . $this->hourly . " #" . $kapenta->db->datetime($this->hourly) . "<br/>\n"
			 . "daily: " . $this->daily . " #" . $kapenta->db->datetime($this->daily) . "<br/>\n";

		$kapenta->session->msgAdmin($msg);

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
		global $kapenta, $db, $user, $theme, $page, $cron;
		$report = '';

		echo "Running task set.. $interval<br/>\n"; flush();

		$kapenta->session->msgAdmin('running task set: ' . $interval);

		$mods = $kapenta->listModules();
		foreach($mods as $modName) {
			$incFile = 'modules/' . $modName . '/inc/cron.inc.php';

			echo "including cron script: " . $incFile . "<br/>\n"; flush();
			

			if (true == $kapenta->fileExists($incFile)) {
				require_once($kapenta->installPath . $incFile);
				$fnName = $modName . '_cron_' . $interval;
				if (true == function_exists($fnName)) { 
					$kapenta->session->msgAdmin('running task set: ' . $incFile . ' (' . $interval . ')');
					$report .= $fnName();
					if ('admin' == $user->role) {
						$cron->log($report, 'black');
						$report = '';
					}
				}
			}
		}
		return $report;
	}

	//==============================================================================================
	//	HTML output
	//==============================================================================================
	
	//----------------------------------------------------------------------------------------------
	//	writes a status message directly to output
	//----------------------------------------------------------------------------------------------
	//arg: msg - status message [string]
	//opt: color - message box color (black|red|green) [string]

	function log($msg, $color = 'black') {
		global $kapenta;
		global $user;
	
		$kapenta->logCron($msg);
		if ('admin' == $user->role) { 
			echo "<div class='chatmessage" . $color . "'>$msg</div>\n"; flush();
		}
	}

}

?>
