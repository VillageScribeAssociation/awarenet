<?

//--------------------------------------------------------------------------------------------------
//*	the registry stores module and other settings in simple key => value format
//--------------------------------------------------------------------------------------------------
//+	FORMAT:
//+	Values are base64 encoded for simplicity, note that keys must be 30 chars or less and unique.
//+	Every key is stored on its own line, to keep things fast (no strpos, split, etc) each value
//+	begins at char 31 of the line and is only base64_decoded when requested by accessor.
//+
//+	KEY NAMING CONVENTION:
//+	Values used by the kapenta core begin with "kapenta.", module configuration keys begin with the
//+	module name and a period.  Key names are all lowercase ASCII characters, periods and numbers.
//+	
//+	FILE NAMING CONVENTION
//+	Keys are grouped by module when serializing, entire .kreg files are cached at first read.

class KRegistry {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $keys;									//_	registry keys [array:dict]
	var $files;									//_	array of loaded registry files [array:string]
	var $path = 'core/registry/';				//_	location of registry files [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KRegistry() {
		$this->keys = array();
		$this->files = array();
	}

	//----------------------------------------------------------------------------------------------
	//.	load keys from file
	//----------------------------------------------------------------------------------------------
	//;	Note that the registry is created before $kapenta, so the framework's IO functions are not
	//;	yet available.

	//arg: prefix - name of registry section, eg 'kapenta', 'blog', 'comments' [string]
	//returns: true on success, false on failure [bool]

	function load($prefix) {
		$regFile = $this->path . $prefix . ".kreg";
		if (false == file_exists($regFile)) { return false; }

		$lines = file($regFile);
		foreach($lines as $line) {
			if ((strlen($line) >= 30) && ('#' != substr($line, 0, 1))) {
				$key = trim(substr($line, 0, 30));
				$value = trim(substr($line, 30));
				$this->keys[$key] = $value;
			}
		}

		if (false == in_array($prefix, $this->files)) { $this->files[] = $prefix; }

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save registry to files
	//----------------------------------------------------------------------------------------------
	//arg: prefix - registry key prefix, usually module name or 'kapenta' [string]

	function save($prefix) {
		$raw = "<?php /*\n";										//%	raw file contents [string]
		foreach($this->keys as $key => $value) {
			if ($prefix == $this->getPrefix($key)) { 
				$raw .= substr($key . str_repeat(' ', 30), 0, 30) . $value . "\n";
			}
		}
		$raw .= "*/ ?>";

		if (false == file_exists($this->path)) { mkdir($this->path); } 

		$regFile = $this->path . $prefix . ".kreg";
		$fH = fopen($regFile, 'w+');
		fwrite($fH, $raw);
		fclose($fH);
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a key exists / has been set
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]
	//returns: true if the key exists, false if not [bool]

	function has($key) {
		if (true == array_key_exists($key, $this->keys)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get key value
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]
	//returns: key value, empty string on failure [string]

	function get($key) {
		$keys = trim(strtolower($key));
		$prefix = $this->getPrefix($key);
		if (false == in_array($prefix, $this->files)) { $this->load($prefix); }
		if (false == array_key_exists($key, $this->keys)) { return ''; }
		$value = base64_decode($this->keys[$key]);
		return $value;
	}

	//----------------------------------------------------------------------------------------------
	//.	set key value
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]
	//arg: value - value of registry key [string]

	function set($key, $value) {
		global $session;
		$prefix = $this->getPrefix($key);
		if (false == in_array($prefix, $this->files)) { $this->load($prefix); }
		$key = trim(strtolower($key));
		$this->keys[$key] = base64_encode($value);
		$this->save($prefix);
		
		if (true == isset($session)) {
			$msg = 'Set registry key: ' . $key . '<br/>' . 'Value: ' . $value;
			$session->msgAdmin($msg, 'ok');
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a registry key
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]
	//returns: true on success, false on failure [bool]

	function delete($key) {
		if (false == array_key_exists($key, $this->keys)) { return false; }
		$prefix = $this->getPrefix($key);

		$newKeys = array();											//% new set of keys [array]
		$found = false;												//%	[bool]

		foreach($this->keys as $k => $v) {
			if ($key == $k) { $found = true; }
			else { $newKeys[$k] = $v; }
		}

		if (true == $found) {
			$this->keys = $newKeys;
			$this->save($prefix);
			return true;
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get prefix of a registry key, ie, the first part of the name
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]	
	//returns: first part of period delimited key name, empty string on failure [string]

	function getPrefix($key) {
		$parts = explode(".", $key);
		return $parts[0];
	}

	//----------------------------------------------------------------------------------------------
	//.	list registry files
	//----------------------------------------------------------------------------------------------
	//opt: fullName - return path and filename is true [bool]
	//returns: array of registry prefixes, or of relative file paths [array:string]
	
	function listFiles($fullName = false) {
		echo "listing files in registry<br/>..."; flush();
		$files = array();
		$d = Dir($this->path);
		$continue = true;
		$max = 1000;
		while (true == $continue) {
			$max--;								// temp bugfixing measure
			if (0 == $max) { 
				echo "too many files<br/>\n";
				return array(); 
			}
			$entry = $d->read();
			if (false == $entry) {
				$continue = false;
			} else {
				if (false != strpos($entry, '.kreg')) {
					if (false == $fullName) { $entry = str_replace('.kreg', '', $entry); }
					$files[] = $entry;
				}
			}
		}
		echo "finished listing files in registry<br/>..."; flush();
		return $files;
	}

	//----------------------------------------------------------------------------------------------
	//.	print a registry section as an html table
	//----------------------------------------------------------------------------------------------
	//arg: prefix - section of the registry we wish to display [string]
	//;	note that the theme is not loaded before the registry, so make basic table

	function toHtml($prefix) {
		$html = '';				//%	return value [string:html]

		$html .= "<table noboder>\n" 
			. "<tr><td class='title'>Key</td><td class='title'>Value</td></tr>\n";

		foreach($this->keys as $key => $value) {
			if ($prefix == $this->getPrefix($key)) {
				$html .= "\t<tr>\n"
					. "\t\t<td class='wireframe'>$key</td>\n"
					. "\t\t<td class='wireframe'>" . base64_decode($value). "</td>\n"
					. "\t</tr>\n";
			}
		}

		$html .= "</table>\n";

		return $html;
	}

}

?>
