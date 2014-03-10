<?

//--------------------------------------------------------------------------------------------------
//*	object to keep track of shell session/environment variables
//--------------------------------------------------------------------------------------------------

class Live_ShellSession {
	
	//----------------------------------------------------------------------------------------------
	//	members
	//----------------------------------------------------------------------------------------------

	var $env;			//_	shell environment variables [array:dict]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Live_ShellSession() {
		$this->env = array();

		// read all environt variables stored in user session
		foreach($_SESSION as $key => $val) {
			if ('KLIVE_' == substr($key, 0, 6)) {
				$key = substr($key, 6);			
				$this->env[$key] = $val;
			}
		}

		// add defaults if not present
		if (false == $this->has('cwd')) { $this->set('cwd', '~/'); }
		//more here...
	}

	//----------------------------------------------------------------------------------------------
	//.	set key value
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a web shell environment variable [string]
	//arg: val - value of environment variable [string]

	function set($key, $val) {
		$this->env[$key] = $val;
		$_SESSION['KLIVE_' . $key] = $val;
	}

	//----------------------------------------------------------------------------------------------
	//.	get key value
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a web shell environment variable [string]
	//returns: value if extant, false if not found [string]

	function get($key) {
		if (false == array_key_exists($key, $this->env)) { return false; }
		return $this->env[$key];
	}

	//----------------------------------------------------------------------------------------------
	//.	discover is a web shell session key exists
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a web shell session key [string]
	//returns: true if environment variable exists, false if not [bool]

	function has($key) {
		if (true == array_key_exists($key, $this->env)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	make html table of session values
	//----------------------------------------------------------------------------------------------
	//returns: html table [string:html]

	function toHtml() {
		global $theme;
		$table = array(array('Key', 'Value'));
		foreach($this->env as $key => $value) {
			$table[] = array($key, $value);
		}
		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	change current working directory
	//----------------------------------------------------------------------------------------------
	//arg: new - change or addition to cwd [string]

	function chdir($new) {
		global $kapenta;

		$cwd = $this->get('cwd');
		if ('/' == substr($new, 0, 1)) {
			$cwd = $new;
		} else {
			$cwd = $cwd . '/' . $new . '/';
		}
		$cwd = $this->reduceRelativeDir($cwd);
		if (true == $kapenta->fs->exists(substr($cwd, 2))) {
			$this->set('cwd', $cwd); 
			return "$cwd [ok]";
		} else {
			return "no such directory: $cwd";
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	make absolute dir from relative one (./, /../, etc
	//----------------------------------------------------------------------------------------------
	//arg: rel - directory path to be reduced [string]

	function reduceRelativeDir($rel) {
		//TODO: this needs fixing up
		$rel = str_replace('//', '/', $rel);
		$rel = str_replace('//', '/', $rel);
		$rel = str_replace('/./', '/', $rel);

		$parts = explode("/", $rel);
		$idx = 0;
		$continue = true;		

		while (true == $continue) {
			if ('~' == $parts[$idx]) { $parts[$idx] = ''; }
			if ('..' == $parts[$idx]) {
				$parts[$idx] = '';
				if ($idx > 0) { $idx--; }
				$parts[$idx] = '';
			}

			$idx++;
			if ($idx >= count($parts)) { $continue = false; }
		}

		$rel = '~/' . implode('/', $parts);
		$rel = str_replace('//', '/', $rel);
		$rel = str_replace('//', '/', $rel);
		return $rel;
	}

	function ls($pattern = '') {
		global $kapenta;
		global $theme;

		$abs = substr($this->get('cwd'), 2);
		$files = $kapenta->listFiles($abs, $pattern);
		$table = array(array('Name', 'Size', 'Type'));

		foreach($files as $file) {
			$size = filesize($kapenta->installPath . $abs . $file);
			$type = 'file';
			if (true == is_dir($kapenta->installPath . $abs . $file)) {
				$type = 'folder';
				$size = 'na';
			}

			$table[] = array($file, $size, $type);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);

		return $html;
	}

}

?>
