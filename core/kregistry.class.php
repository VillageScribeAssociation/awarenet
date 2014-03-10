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
	var $path = 'data/registry/';				//_	location of registry files [string]
	var $lockTTL = 3;							//_	number of seconds before locks expire [string]
	var $maxFailures = 5;							//_	failed locks [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: baseDir - optional absolute location of kapenta install [string]

	function KRegistry($baseDir = '') {
		$this->keys = array();
		$this->files = array();
		$this->path = $baseDir . $this->path;
	}

	//----------------------------------------------------------------------------------------------
	//.	load keys from file
	//----------------------------------------------------------------------------------------------
	//;	Note that the registry is created before $kapenta, so the framework's IO functions are not
	//;	yet available.

	//arg: prefix - name of registry section, eg 'kapenta', 'blog', 'comments' [string]
	//returns: true on success, false on failure [bool]

	function load($prefix) {
		$regFile = $this->path . $prefix . ".kreg.php";

		if (false == file_exists($regFile)) { 
			//--------------------------------------------------------------------------------------
			//	check that registry directory exists
			//--------------------------------------------------------------------------------------
			if (false == file_exists('data/')) { @mkdir('data/'); }
			if (false == file_exists('data/registry/')) { @mkdir('data/registry/'); }

			//--------------------------------------------------------------------------------------
			//	unknown prefix, try previous locations where this might be stored
			//--------------------------------------------------------------------------------------
			$oldFile = 'core/registry/' . $prefix . ".kreg";
			if (file_exists($oldFile)) {
				$moved = copy($oldFile, $regFile);
				if (false == $moved) { return false; }
			}

			$oldFile = 'core/registry/' . $prefix . ".kreg.php";
			if (file_exists($oldFile)) {
				$moved = copy($oldFile, $regFile);
				if (false == $moved) { return false; }
			}

			if (false == file_exists($regFile)) { return false; }
		}

		$lines = array();
		$waiting = true;

		while (true == $waiting) {
			$lock = $this->getLock($prefix);

			if (('none' == $lock['type']) || ('read' == $lock['type'])) {
				if ('none' == $lock['type']) { $this->setLock($prefix, 'read'); }	//	lock file
				$lines = file($regFile);											//	read file

				$lock = $this->getLock($prefix);				//	check lock after read

				if ('read' == $lock['type']) {
					$this->setLock($prefix, 'none');			//	clear our read lock
					$waiting = false;
				}

				//	if not locked then continue (another concurrent read should not affect us)
				if ('none' == $lock['type']) { $waiting = false; }
			}
			
			if (true == $waiting) { sleep(1); }		//	wait one second before trying again
		}

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
	//.	load all registry files
	//----------------------------------------------------------------------------------------------

	function loadAll() {
		$prefixes = $this->listFiles();
		foreach($prefixes as $prefix) { $this->load($prefix); }
	}

	//----------------------------------------------------------------------------------------------
	//.	save registry to files
	//----------------------------------------------------------------------------------------------
	//arg: prefix - registry key prefix, usually module name or 'kapenta' [string]
	//return: true on success, false on failure [bool]

	function save($prefix) {
		if ('' == trim($prefix)) { return false; }

		$regFile = $this->path . $prefix . ".kreg.php";		//%	disk file for this prefix [string]
		$waiting = true;									//%	still waiting for lock [bool]

		//------------------------------------------------------------------------------------------
		//	make the raw file
		//------------------------------------------------------------------------------------------

		$raw = "<?php /*\n";								//%	raw file contents [string]

		foreach($this->keys as $key => $value) {
			if ($prefix == $this->getPrefix($key)) { 
				$raw .= substr($key . str_repeat(' ', 30), 0, 30) . $value . "\n";
			}
		}

		$raw .= "*/ ?>";

		//------------------------------------------------------------------------------------------
		//	get a write lock on the registry file
		//------------------------------------------------------------------------------------------
		while(true == $waiting) {
			$lock = $this->getLock($prefix);
			if ('none' == $lock['type']) {
				$this->setLock($prefix, 'write');
				$waiting = false;
			}
		}

		//------------------------------------------------------------------------------------------
		//	write new contents of this prefix
		//------------------------------------------------------------------------------------------
		$fH = fopen($regFile, 'w+');
		fwrite($fH, $raw);
		fclose($fH);

		//------------------------------------------------------------------------------------------
		//	remove the lock
		//------------------------------------------------------------------------------------------
		$this->setLock($prefix, 'none');

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	check for and return any file lock
	//----------------------------------------------------------------------------------------------
	//arg: prefix - name of a registry section [string]

	function getLock($prefix) {
		global $kapenta;

		$lock = array(
			'type' => 'none',
			'expires' => time() + $this->lockTTL,
			'check' => 'bad'
		);
		$lockFile = $this->path . $prefix . '.lock.php';

		if (true == file_exists($lockFile)) {
			$raw = @file_get_contents($lockFile);
			if (false === $raw) {
				//	file was deleted as we loaded it, try again
				sleep(1);
				//echo "sleeeping...<br/>"; flush();
				return $this->getLock($prefix);

			} else {
				$parts = explode("\n", $raw);
				if (array_key_exists(0, $parts)) { $lock['type'] = $parts[0]; }
				if (array_key_exists(1, $parts)) { $lock['expires'] = $parts[1]; }
				if (array_key_exists(2, $parts)) { $lock['check'] = $parts[2]; }

				if ('bad' == $lock['check']) {
					// file was partially written, try again
					sleep(1);
					$this->maxFailures--;
					if ($this->maxFailures > 0) {
						$lock = $this->getLock($prefix);
					} else {
						// delete the lock file
						unlink($lockFile);
					}
				}

			}
		}

		//	clear any expired lock while we're at it
		if ((time() > (int)$lock['expires']) && ('none' != $lock['type'])) {
			$this->setLock($prefix, 'none');
		}

		return $lock;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a lock file for a precified prefix
	//----------------------------------------------------------------------------------------------
	//:	note that setting a lock of type 'none' will delete any existing lock file
	//arg: prefix - registry section name [string]
	//arg: type - may be 'read', 'write' or 'none'
	//returns: true on success, false on failure [bool]

	function setLock($prefix, $type) {
		$lockFile = $this->path . $prefix . '.lock.php';

		//------------------------------------------------------------------------------------------
		//	clear the lock
		//------------------------------------------------------------------------------------------
		if ('none' == $type) {
			if (file_exists($lockFile)) { return @unlink($lockFile); }
			return false;
		}

		$raw = $type . "\n" . (string)(time() + $this->lockTTL) . "\nOK\n";
		$check = $this->filePutContents($lockFile, $raw);
		return $check;
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
	//arg: forceReload - reload file from disk to see changes made by other threads [bool]
	//returns: key value, empty string on failure [string]

	function get($key, $forceReload = false) {
		$keys = trim(strtolower($key));
		$prefix = $this->getPrefix($key);
		if (true == $forceReload) { $this->load($prefix); }
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
	//returns: true on success, false on failure [bool]

	function set($key, $value) {
		global $kapenta;
		$prefix = $this->getPrefix($key);
		if (false == in_array($prefix, $this->files)) { $this->load($prefix); }
		$key = trim(strtolower($key));
		$this->keys[$key] = base64_encode($value);
		$check = $this->save($prefix);
		
		$this->log($prefix, 'set', $key, $value);

		if ('object' == gettype($kapenta->session)) {
			$msg = 'Set registry key: ' . $key . '<br/>' . 'Value: ' . $value;
			$kapenta->session->msgAdmin($msg, 'ok');
		}

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a registry key
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a registry key [string]
	//returns: true on success, false on failure [bool]

	function delete($key) {
		if (false == array_key_exists($key, $this->keys)) { return false; }
		$prefix = $this->getPrefix($key);
		if ('' == trim($prefix)) { return false; }

		$newKeys = array();											//% new set of keys [array]
		$found = false;												//%	[bool]

		foreach($this->keys as $k => $v) {
			if ($key == $k) { $found = true; }
			else { $newKeys[$k] = $v; }
		}

		if (true == $found) {
			$this->log($prefix, 'delete', $key, '');
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
	//;	IMPORTANT: this does not use $kapenta->fileSearch() because registry is initialized
	//;	before KSystem is, so keep the ugly Dir business plox.
	//opt: fullName - return path and filename is true [bool]
	//returns: array of registry prefixes, or of relative file paths [array:string]	

	function listFiles($fullName = false) {
		$files = array();						//%	return value [array]
		$d = Dir($this->path);					
		$continue = true;						//%	loop control [bool]
		$max = 1000;							//%	temp, sanity check [int]
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
					if (false == $fullName) {
						$entry = str_replace('.kreg.php', '', $entry);
						$entry = str_replace('.kreg', '', $entry);
					}
					$files[] = $entry;
				}
			}
		}

		return $files;
	}

	//----------------------------------------------------------------------------------------------
	//.	return a subset of keys matching from the given prefix
	//----------------------------------------------------------------------------------------------
	//arg: prefix - registry file to load before searching [string]
	//arg: begins - registry key starts with this [string]
	//returns: set of registry keys and values [dict]

	function search($prefix, $begins) {
		$matches = array();						//%	return value [string]
		$this->load($prefix);
		foreach($this->keys as $key => $value64) {
			if ($begins == substr($key, 0, strlen($begins))) { 
				$matches[$key] = base64_decode($value64);
			}
		}
		return $matches;
	}

	//----------------------------------------------------------------------------------------------
	//.	log a registry change
	//----------------------------------------------------------------------------------------------

	function log($prefix, $event, $key, $value) {
		global $user;

		$logFile = $this->path . $prefix . '.hist.php';

		$userName = 'unknown';
		if (isset($user)) { $userName = $user->username; }

		$value = str_replace("\r", '\r', $value);
		$value = str_replace("\r", '\n', $value);

		$line = ''
		 . (isset($user) ? $user->username : 'unknown') . ' '
		 . gmdate("Y-m-d H:i:s", time()) . ' '
		 . 'prefix:' . $prefix . ' '
		 . 'event:' . $event . ' '
		 . 'key:' . $key . ' '
		 . 'value:' . $value . ' '
		 . "\n";

		$fH = fopen($logFile, 'a+');
		fwrite($fH, $line);
		fclose($fH);
	}

	//----------------------------------------------------------------------------------------------
	//.	print a registry section as an html table
	//----------------------------------------------------------------------------------------------
	//arg: prefix - section of the registry we wish to display [string]
	//;	note that the theme is not loaded before the registry, so make basic table

	function toHtml($prefix) {
		$html = '';				//%	return value [string:html]
		
		$html .= "<table noboder width='100%'>\n" 
			. "<tr><td class='title'>Key</td><td class='title'>Value</td></tr>\n";

		$this->load($prefix);

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

	//----------------------------------------------------------------------------------------------
	//.	set the contents of a file, will create directories if they do not exist
	//----------------------------------------------------------------------------------------------
	//:	note that this should only be used by registry object.
	//
	//arg: fileName - relative to installPath [string]
	//arg: contents - new file contents [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - protective wrapper [bool]
	//opt: m - file mode [string]
	//returns: true on success, false on failure [bool]

	function filePutContents($fileName, $contents, $inData = false, $phpWrap = false, $m = 'wb+') {
        global $kapenta;		

        // add php wrapper to file
		if (true == $phpWrap) { $contents = $this->wrapper . $contents . "\n*/ ?>"; }

		// note that file_put_contents() was added in PHP 5, we do it this way to support PHP 4.x
		$fH = fopen($fileName, $m);							//	specify binary for Windows
		if (false === $fH) { return false; }				//	can fH ever be 0?

		//	wait for lock
		$lock = false;
		$counter = 20;										//	make registry setting?
		while (false == $lock) {
			$lock = flock($fH, LOCK_EX);
			if (false == $lock) { sleep(1); }
			$counter--;
			if (0 == $counter) {
				$kapenta->session->msgAdmin('Could not lock file: ' . $regFile, 'bad');
				return false;
			}
		}

		fwrite($fH, $contents);
		$lock = flock($fH, LOCK_UN);
		fclose($fH);
		return true;
	}

}

?>
