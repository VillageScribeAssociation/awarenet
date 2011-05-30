<?

//--------------------------------------------------------------------------------------------------
//*	object for working with kapenta log files, particularly the pageview log
//--------------------------------------------------------------------------------------------------

class Admin_LogFile {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $fileName = '';			//%	location of log file relative to installPath [string]
	var $entries;				//%	contents of log file [array:array]
	var $loaded = false;		//%	set to true when log file has been parsed [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: fileName - location of XML log file relative to installPath [string]

	function Admin_Logfile($fileName = '') {
		if ('' != $fileName) { $this->load($fileName); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load/parse a kapenta XML log file, brittle but it works
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location of XML log file relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function load($fileName) {
		global $kapenta;
		if (false == $kapenta->fileExists($fileName)) { return false; }

		$raw = $kapenta->fileGetContents($fileName, true, false);
		$startPos = strpos($raw, '<entry>');
		$raw = ' ' . substr($raw, $startPos);
		
		$continue = true;
		$startPos = 0;
		while (true == $continue) {
			$endPos = strpos($raw, '</entry>');
			if (false == $endPos) { 
				$continue = false; 
			} else {
				$entry = substr($raw, $startPos, ($endPos + 8) - $startPos);
				$this->addEntry($entry);
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add a raw entry
	//----------------------------------------------------------------------------------------------

	function addEntry($entry) {
		$lines = explode("\n", $entry);
		foreach($lines as $line) {
			$line = trim($line);
			$startPos = strpos($line, '>');
			if (false != $startPos) {
				$type = substr($line, 1, $startPos);
				echo $type . " \n";
			}
		}
	}

}

?>
