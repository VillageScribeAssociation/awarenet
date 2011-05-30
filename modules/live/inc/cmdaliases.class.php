<?

//--------------------------------------------------------------------------------------------------
//*	object to manage, store and interpret command aliases
//--------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------
	//.	find canonical version of aliased command
	//----------------------------------------------------------------------------------------------
	//arg: match - command alias to find [string]

class Live_CmdAliases {

	//----------------------------------------------------------------------------------------------
	//.	member variables
	//----------------------------------------------------------------------------------------------

	var $aliases;			//_	array of alias => canonical [array:dict]
	var $fileName = '';		//_	location of aliases file [string]
	var $loaded = false;	//_	set to true when aliases file has been loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Live_CmdAliases() {
		$this->aliases = array();
		$this->fileName = 'modules/live/conf/aliases.txt.php'; 
		$this->load();
	}

	//----------------------------------------------------------------------------------------------
	//.	load aliases file
	//----------------------------------------------------------------------------------------------
	//opt: fileName - name of aliases file to load [string]
	//returns: true on success, false on failure [bool]

	function load($fileName = '') {
		global $kapenta;

		// check that aliases file exists, try create if it doesn't
		if (false == $kapenta->fileExists($this->fileName)) {
			$this->aliases = array('cls' => 'live.clear');
			$this->save();
		}

		if ('' == $fileName) { $fileName = $this->fileName; }

		// read aliases into array
		$raw = $kapenta->fileGetContents($fileName, false, true);
		if (false == $raw) { return false; }		
		$lines = explode("\n", $raw);
		
		foreach($lines as $line) {
			if (false != strpos($line, ":=")) {
				$parts = explode(":=", $line, 2);
				$alias = trim($parts[0]);
				$canonical = trim($parts[1]);
				$this->aliases[$alias] = $canonical;
			} 
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	save aliases file
	//----------------------------------------------------------------------------------------------
	
	function save() {
		global $kapenta;
		$raw = '';

		foreach($this->aliases as $alias => $canonical) {
			if ('' != trim($canonical)) { $raw .= "$alias := $canonical\n"; }
		}
		$kapenta->filePutContents($this->fileName, $raw, false, true); 
	}

	//----------------------------------------------------------------------------------------------
	//.	add an alias
	//----------------------------------------------------------------------------------------------

	function add($alias, $canonical) {
		// TODO: check canonical
		$this->aliases[$alias] = $canonical;
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//.	remove an alias
	//----------------------------------------------------------------------------------------------

	function remove() {
		if (false == array_key_exists($alias, $this->aliases)) { return false; }
		$this->aliases[$alias] = '';
		$this->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	find canonical version of aliased command
	//----------------------------------------------------------------------------------------------
	//arg: match - command alias to find [string]

	function find($match) {
		$match = trim($match);
		if (true == array_key_exists($match, $this->aliases)) { return $this->aliases[$match]; }
		return $match;
	}

	//----------------------------------------------------------------------------------------------
	//.	reset alias table to default
	//----------------------------------------------------------------------------------------------
	
	function clear() {
		$this->aliases = array();
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//.	load default aliases from all modules
	//----------------------------------------------------------------------------------------------
	//;	The default aliases file for a module should be in /modules/%%module%%/shell/aliases.txt.php
	//returns: html report [string:html]

	function loadDefault() {
		global $kapenta;
		$report = '';				//%	return value [string]

		$modules = $kapenta->listModules();
		foreach($modules as $module) {
			$report .= "scanning module: $module<br/>";
			$fileName = "modules/" . $module . "/shell/aliases.txt.php";
			if (true == $kapenta->fileExists($fileName)) {
				$report .= "loading: $fileName<br/>";
				$this->load($fileName);
			}
		}
		$this->save();
		$report .= "<b>" . count($this->aliases) . " aliases loaded.</b><br/>";
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	return aliases list as html table
	//----------------------------------------------------------------------------------------------
	//returns: html table [string]

	function toHtml() {
		global $theme;
		$table = array(array('Alias', 'Canonical'));
		foreach($this->aliases as $alias => $canonical) { $table[] = array($alias, $canonical); }
		$html = $theme->arrayToHtmlTable($table, true, true);
		if (0 == count($this->aliases)) {
			$html .= "<b>no aliases recorded, to fix: <tt>live.aliases --reset</tt></b>";
		}
		return $html;
	}

}

?>
