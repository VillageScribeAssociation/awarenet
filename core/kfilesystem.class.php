<?php

//--------------------------------------------------------------------------------------------------
//*	Utility object for interacting with the filesystem
//--------------------------------------------------------------------------------------------------

class KFilesystem {

	//----------------------------------------------------------------------------------------------
	//.	properties
	//----------------------------------------------------------------------------------------------

	var $installPath = '';			//_	location of this instance on disk [string]
	
	var $wrapper = "<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*\n";

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: installPath - location of this kapenta installation on disk [string]

	function KFilesystem($installPath) {
		$this->installPath = $installPath;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover which object owns a file (DEPRECATED)
	//----------------------------------------------------------------------------------------------
	//arg: path - location of file relative to installPath [string]
	//returns: dict of 'module', 'model' and 'UID', empty array on failure [array]

	function getOwner($path) {
		global $kapenta;
		$owner = array();				//%	return value [dict]

		$mods = $kapenta->listModules();
		foreach($mods as $modName) {
			$incFile = 'modules/' . $modName . '/inc/files.inc.php';
			$fnName = $modName . '_fileOwner';
			if (true == $this->exists('modules/' . $modName . '/inc/files.inc.php')) {

				include_once $this->installPath . $incFile;
				if (true == function_exists($fnName)) {
					$owner = $fnName($path);
					if (count($owner) > 0) { return $owner; }
				}
			}
		}

		return $owner;
	}

	//----------------------------------------------------------------------------------------------
	//.	check whether a file exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if file exists, false if not [bool]

	function exists($fileName) {
		global $kapenta;
		$fileName = $this->checkName($fileName);
		if (true == file_exists($kapenta->installPath . $fileName)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	check a fileName (path) before use
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true, fileName must be inside ../data/ [bool]
	//returns: clean fileName, or false on failure [string][bool]
	
	function checkName($fileName, $inData = false) {
		global $kapenta;

		$fileName = str_replace('//', '/', $fileName);
		$ipLen = strlen($kapenta->installPath);
		$fileNameLc = strtolower($fileName);		

		//	Unicode directory traversal, see: http://www.schneier.com/crypto-gram-0007.html
		$fileNameLc = str_replace("%c0%af", '/', $fileNameLc);
		$fileNameLc = str_replace("%c0%9v", '/', $fileNameLc);
		$fileNameLc = str_replace("%c1%1c", '/', $fileNameLc);

		//	Precent encoded
		$fileNameLc = str_replace("%2f", '/', $fileNameLc);
		$fileNameLc = str_replace("%5c", '/', $fileNameLc);
		$fileNameLc = str_replace("%2e", '.', $fileNameLc);

		//	Classic directory traversal
		if (strpos(' ' . $fileNameLc, '../') != false) { return false; }
		if (strpos(' ' . $fileNameLc, '..\\') != false) { return false; }

		//	Make absolute locations relative to installPath, case insentitive
		if (strlen($fileName) >= $ipLen) {
			if (strtolower($kapenta->installPath) == substr($fileNameLc, 0, $ipLen)) { 
				$fileName = substr($fileName, $ipLen);
			}
		}

		//	Check that location is inside of ../data/ if required
		if ((true == $inData) && ('data/' != substr($fileNameLc, 0, 5))) { return false; }

		return $fileName;
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure that a directory exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - path relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//returns: true on success, false on failure [bool]

	function makePath($fileName, $inData = false) {
		global $kapenta;

		$fileName = $this->checkName($fileName, $inData);
		if (false == $fileName) { return false; }
		$dirName = dirname($fileName);

		if (true == file_exists($kapenta->installPath . $dirName . '/')) { 
			// already exists
			return true; 

		} else {
			// doesn't exist, check for and add missing subdirs one at a time
			$base = $kapenta->installPath;
			$subDirs = explode('/', $dirName);
			foreach($subDirs as $sdir) {
				//	note that 'recursive' option for mkdir was only added in PHP 5.0.0
				$base = $base . $sdir . '/';
				if (false == file_exists($base)) {
					$created = mkdir($base); 
					if (false == $created) { return false; }
				}
			}
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the contents of a file (entire file returned as string)
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - if true any php wrapper will be removed [bool]
	//returns: entire file contents, or false on failure [string][bool]

	function get($fileName, $inData = false, $phpWrap = false) {
		$fileName = $this->checkName($fileName, $inData);
		if (false == $fileName) { return false; }

		// note that file_get_contents() was added in PHP 4.3, we do it this way to support PHP 4.x
		$fH = @fopen($this->installPath . $fileName, 'rb');		//	specify binary for Windows
		if (false === $fH) { return false; }					//	check that file was opened
		$size = filesize($this->installPath . $fileName);
		if (0 == $size) { return ''; }
		$contents = fread($fH, $size);
		fclose($fH);
		if (true == $phpWrap) { $contents = $this->removePhpWrapper($contents); }
		return $contents;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the contents of a file, will create directories if they do not exist
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//arg: contents - new file contents [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - protective wrapper [bool]
	//opt: m - file mode [string]
	//returns: true on success, false on failure [bool]

	function put($fileName, $contents, $inData = false, $phpWrap = false, $m = 'wb+') {
		$fileName = $this->checkName($fileName, $inData);
		if (false == $fileName) { return false; }
		if (false == $this->makePath($fileName, $inData)) { return false; }

		// add php wrapper to file
		if (true == $phpWrap) { $contents = $this->wrapper . $contents . "\n*/ ?>"; }

		// note that file_put_contents() was added in PHP 5, we do it this way to support PHP 4.x
		$fH = @fopen($this->installPath . $fileName, $m);		//	specify binary for Windows
		if (false === $fH) { return false; }					//	can fH ever be 0?

		//	wait for lock
		$lock = false;
		$counter = 20;											//	make registry setting?
		while (false == $lock) {
			$lock = flock($fH, LOCK_EX);
			if (false == $lock) { sleep(1); }
			$counter--;
			if (0 == $counter) {
				$session->msgAdmin('Could not lock file: ' . $regFile, 'bad');
				return false;
			}
		}

		fwrite($fH, $contents);
		$lock = flock($fH, LOCK_UN);
		fclose($fH);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function delete($fileName, $inData = false) {
		if (false == $this->checkName($fileName, $inData)) { return false; }
		if (false == $this->exists($fileName)) { return false; }
		$check = @unlink($this->installPath . $fileName);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a directory
	//----------------------------------------------------------------------------------------------

	function rmDir($directory, $inData = false) {
		if (false == $this->checkName($directory, $inData)) { return false; }
		if (false == $this->exists($directory)) { return false; }
		$check = @rmdir($this->installPath . $directory);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	list the contents of a directory, excluding subdirectories
	//----------------------------------------------------------------------------------------------
	//arg: dir - directory path relative to $kapenta->installPath [string]
	//opt: ext - filter to this file extension, case insensitive [string]
	//opt: onlySubDirs - only returns subdirectories if true [bool]
	//returns: array of file paths relative to installPath [array:string]

	function listDir($dir, $ext = '', $onlySubDirs = false) {
		$list = array();									//%	return value [array:string]

		if (('' == $dir) || ('/' != substr($dir, strlen($dir) - 1))) { $dir = $dir . '/'; }
		$fullPath = $this->installPath . $dir;				//%	relative to root [string]
		$dir = $this->checkName($fullPath);
		$ext = strtolower($ext);
		$extLen = strlen($ext);								//%	length of ext, if any [int]

		$d = dir($fullPath);								//%	directory [object:directory]
		$continue = true;									//%	loop control [bool]
		while (true == $continue) {
			$entry = $d->read();
			$ok = true;

			if (false == $entry) { 
				$ok = false;
				$continue = false;
			}

			if ((true == $ok) && (('.' == $entry) || ('..' == $entry))) { $ok = false; }

			if (true == $ok) {
				$isDir = is_dir($fullPath . $entry);
				if (true == $isDir) { $entry = $entry . '/'; }
				if ($isDir != $onlySubDirs) { $ok = false; }
			} 

			if ((true == $ok) && ('' != $ext) && (strlen($entry) >= $extLen)) {
				$entryLen = strlen($entry);									//%	[int]
				$match = strtolower(substr($entry, $entryLen - $extLen));	//%	[string]
				if ($ext != $match) { $ok = false; }
			}

			if (true == $ok) { $list[] = $dir . $entry; }
		}

		return $list;
	}

	//----------------------------------------------------------------------------------------------
	//.	search for files with a given extension, optionally in some subdirectory
	//----------------------------------------------------------------------------------------------
	//opt: dir - starting directory [string]
	//opt: ext - file extension, eg '.block.php' [string]
	//opt: folders - add directories to the results, default is false [bool]
	//returns: array of file locations [array:string]
	//;	not very efficient, could be improved

	function search($dir = '', $ext = '', $folders = false) {
		$list = $this->listDir($dir, $ext, false);			//%	return value [array:string]
		$subDirs = $this->listDir($dir, '', true);
		//echo "<pre>\n"; print_r($subDirs); echo "</pre><br/>\n";
		foreach ($subDirs as $subDir) {
			$more = $this->search($subDir, $ext);
			foreach($more as $item) { $list[] = $item; }
			if (true == $folders) { $list[] = $subDir; }
		}
		return $list;
	}

	//----------------------------------------------------------------------------------------------
	//|	determines if a file/dir exists and is readable + writeable
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if exists, else false [bool]

	function isExtantRw($fileName) {
		$fileName = $this->checkName($fileName);
		if (false == $fileName) { return false; }		// bad file name
		$absolute = $this->installPath . $fileName;
		if (file_exists($absolute)) {
			if (false == is_readable($absolute)) { return false; }
			if (false == is_writable($absolute)) { return false; }
		} else { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove php wrapper
	//----------------------------------------------------------------------------------------------
	//arg: content - string to remove wrapper from [string]
	//returns: content without wrapper [string]

	function removePhpWrapper($content) {
		$content = trim($content);
		$cL = strlen($content);
		if ($cL < 10) { return $content; }	// too short to be wrapped
		if ("\n*/ ?>" == substr($content, $cL - 6)) { $content = substr($content, 0, ($cL - 6)); }
		if ("\\n*/ ?>" == substr($content, $cL - 7)) { $content = substr($content, 0, ($cL - 7)); }
		if ("<? /*\n" == substr($content, 0, 6)) { $content = substr($content, 6); }
		if ("<? /*\r" == substr($content, 0, 6)) { $content = substr($content, 6); }
		if ($this->wrapper == substr($content, 0, strlen($this->wrapper))) 
			{ $content = substr($content, strlen($this->wrapper)); }

		return $content;
	}

	//----------------------------------------------------------------------------------------------
	//.	get sha1 hash of file
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: sha1 hash of file, empty string on failure [string]

	function sha1($fileName) {
		$hash = '';
		if (true == $this->exists($fileName)) {
			$hash = sha1_file($this->installPath . $fileName);
		}
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	get size of file 
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: size of file in bytes, -1 on failure [int]

	function size($fileName) {		
		$size = -1;														//%	return value [int]
		if (true == $this->exists($fileName)) {
			$size = filesize($this->installPath . $fileName);
		}
		return $size;
	}

	//----------------------------------------------------------------------------------------------
	//.	copy a file
	//----------------------------------------------------------------------------------------------
	//arg: src - location relative to installPath [string]
	//arg: dest - location relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function copy($src, $dest) {
		$check = false;								//%	return false [bool]
		$src = $this->checkName($src);
		$dest = $this->checkName($dest);
		if ((false == $src) || (false == $dest)) { return $check; }
		if (false == $this->exists($src)) { return $check; }
		$check = $this->makePath($dest);
		if (false == $check) { return false; }
		$check = copy($this->installPath . $src, $this->installPath . $dest);
		return $check;
	}

}


?>
