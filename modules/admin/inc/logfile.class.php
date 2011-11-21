<?

	require_once($kapenta->installPath . 'core/khtml.class.php');

//--------------------------------------------------------------------------------------------------
//*	object for working with kapenta log files, particularly the pageview log
//--------------------------------------------------------------------------------------------------

class Admin_LogFile {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $inFile = '';			//%	location of log file relative to installPath [string]
	var $outFile = '';			//%	(temporary) output to file [string]
	var $type = 'pageview';		//%	log file format [string]
	var $loaded = false;		//%	set to true when log file has been parsed [bool]
	var $format = 'apache';		//%	output mode [string]

	var $inFh = 0;				//%	input file handle [string]
	var $outFh = 0;				//%	output file name [int

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: inFile - log file, relative to installPath [string]
	//opt: outFile - file to recieve output, relative to installPath [string]

	function Admin_Logfile($inFile = '', $outFile = '', $format = 'apache') {
		global $kapenta;

		///TODO: detect log type here

		$this->format = $format;
		if ('' == $outFile) {
			$outFile = 'data/temp/' . $kapenta->time() . '-' . $kapenta->createUID() . '.tmp';
		}

		if ('' != $inFile) { $this->load($inFile, $outFile); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load/parse a kapenta XML log file, brittle but it works
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location of XML log file relative to installPath [string]
	//returns: true on success, false on failure [bool]
	//TODO: make this stream based, rather than loading the whole file at once

	function load($inFile, $outFile) {
		global $kapenta;

		$continue = true;					//%	loop condition [bool]
		$stub = true;						//%	set to true at end of stub program [bool]
		$buffer = '';						//%	holds current entry [string]

		//------------------------------------------------------------------------------------------
		//	open input and output files
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->fileExists($inFile)) { return false; }
		$this->inFile = $inFile;
		$this->inFh = fopen($kapenta->installPath . $inFile, 'r+');

		$this->outFile = $outFile;
		$this->outFh = fopen($kapenta->installPath . $outFile, 'w+');

		//------------------------------------------------------------------------------------------
		//	read file one line at a time
		//------------------------------------------------------------------------------------------
		while (true == $continue) {
			$line = fgets($this->inFh);
			if (false == $line) { 
				$continue = false; 
			} else {
				if (false == $stub) { $buffer .= trim($line) . "\n"; }
			}

			if ('</entry>' == trim($line)) { 
				$entry = array();

				//----------------------------------------------------------------------------------
				// convert log entry to array
				//----------------------------------------------------------------------------------
				switch($this->type) {
					case 'pageview':	$entry = $this->pageviewToArray($buffer);	break;
				}

				//----------------------------------------------------------------------------------
				// throw entry to appropriate serializer
				//----------------------------------------------------------------------------------
				switch($this->format) {
					case 'apache':		$this->addEntryApache($entry);				break;
				}

				//----------------------------------------------------------------------------------
				// clear the buffer
				//----------------------------------------------------------------------------------
				$buffer = '';
			}

			if ((true == $stub) && ('?>' == trim($line))) { $stub = false; }
		}

		//------------------------------------------------------------------------------------------
		//	done, close input and output files
		//------------------------------------------------------------------------------------------
		fclose($this->inFh);
		fclose($this->outFh);
	}

	//----------------------------------------------------------------------------------------------
	//.	output in html format
	//----------------------------------------------------------------------------------------------
	//arg: xml - a single pageview log entry in raw XML	format [string]
	//returns: the same entry as an associative array [array]

	function pageviewToArray($xml) {
		global $utils;
		$entry = array();		//%	return value [array]

		//TODO: set default values for these
		$fields = array(
			'timestamp' => '0',
			'mysqltime' => '',
			'user' => 'public',
			'remotehost' => 'unknown', 
			'remoteip' => '0.0.0.0',
			'request' => '',
			'referrer' => '',
			'uid' => '',
			'useragent' => 'unknown'
		);

		foreach($fields as $field => $value) {
			$substr = $utils->strdelim($xml, '<' . $field . '>', '</' . $field . '>');
			if ('' != $substr) { $entry[$field] = $substr; }
		}

		return $entry;
	}

	//----------------------------------------------------------------------------------------------
	//.	output in apache format
	//----------------------------------------------------------------------------------------------

	function addEntryApache($entry) {
		global $kapenta;

		$line = ''
			. $entry['remoteip']
			. ' - - '
			. date('[d/M/Y:H:i:s +0000]', $entry['timestamp']) . ' '
			. "\"GET " . $entry['request'] . " HTTP/1.1\" 200 - " 
			. "\"" . $kapenta->serverPath . $entry['request'] . "\" "
			. "\"" . $entry['useragent'] . "\""
			. "\n";

		fwrite($this->outFh, $line);
	}

	//----------------------------------------------------------------------------------------------
	//.	make analog report
	//----------------------------------------------------------------------------------------------
	//returns: HTML report as generated by analog [string]

	function analog() {
		global $kapenta;

		$reportFile = 'data/temp/' . $kapenta->time() . '-' . $kapenta->createUID() . '.html';
		
		$shellCmd = 'analog ' . $kapenta->installPath . $this->outFile
			 . ' +O' . $kapenta->installPath . $reportFile;

		//echo $shellCmd . "<br/>\n";

		$result = shell_exec($shellCmd);
		$raw = $kapenta->fileGetContents($reportFile);

		$parser = new KHTMLParser($raw);
		$report = $parser->output;

		return $report;
	}

}

?>
