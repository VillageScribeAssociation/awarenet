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
	//.	load the lastrun.cron file (by default it is in /data/)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, fals on failure [bool]

	function load() {
		global $kapenta;
		$raw = $kapenta->fileGetContents($this->fileName);
		if (false == $raw) { return false; }
		$lines = explode("\n", $raw);

		foreach($lines as $line) {
			if (false !== strpos($line, ':')) {
				if (false != strpos($line, '#')) { $line = substr($line, 0, strpos($line, '#')); }
				$parts = explode(":", $line, 2);
				switch($parts[0]) {
					case 'tenmins': $this->tenmins = (int)$parts[1];	break;
					case 'hourly': $this->hourly = (int)$parts[1];		break;
					case 'daily': $this->daily = (int)$parts[1];		break;
				}
			}
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load the lastrun.cron file (by default it is in /data/
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta, $db;
		$txt = "<? /*\n"
			 . "tenmins: " . $this->tenmins . " # " . $db->datetime($this->tenmins) . "\n"
			 . "hourly: " . $this->hourly . " # " . $db->datetime($this->hourly) . "\n"
			 . "daily: " . $this->daily . " # " . $db->datetime($this->daily) . "\n"
			 . "*/ ?>";

		$kapenta->filePutContents($this->fileName, $txt);		
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

		if (($now - $this->hourly) > 86400) {
			$hour = (int)date('G', $now);
			if (($hour > 3) && ($hour < 4)) {				// only between 3 and 4 in the morning
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
