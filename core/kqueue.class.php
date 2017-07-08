<?php

//--------------------------------------------------------------------------------------------------
//*	Scalabale queues used for async events and p2p data
//--------------------------------------------------------------------------------------------------
//+
//+	queue file naming convention
//+
//+		data/queue/{name}/{created}_{UID}.xml		- plain file	
//+		data/queue/{name}/overflow/{created}_{UID}.gz	- overflow file
//+	
//+	these queues are not strictly FIFO, if very large numbers of items are added then content will
//+	be moved to an overflow directory and compressed.  This is necessary to maintain efficiency when
//+	large backlogs of p2p data build up.
//+
//+	When files are added to the overflow directory, they are first compressed with gzip.  If there
//+	are more than $maxFiles in the overflow directory the two smallest files will be merged into a
//+	new gzip archive.
//+
//+	When pulling data from the queue, if empty the overflow directory will be checked for any 
//+	outstanding gzip files, and the smallest extracted into the queue directory.  

class KQueue {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $baseDir = 'data/queue/';
	var $queueDir = '';
	var $overflowDir = '';	
	var $maxFiles = 128;
	var $name = '';

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KQueue($name) {
		global $kapenta;

		$this->name = $name;
		$this->queueDir = $this->base . $name . '/';
		$this->overflowDir = $this->queueDir . 'overflow/';

		if (false === $kapenta->fs->exists($this->base)) {
			$kapenta->fs->makePath($this->base);
		}


		if (false === $kapenta->fs->exists($this->queueDir)) {
			$kapenta->fs->makePath($this->queueDir);
		}


		if (false === $kapenta->fs->exists($this->base . $this->name . '/overflow/')) {
			$kapenta->fs->makePath($this->overflowDir);
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add an item to this queue
	//----------------------------------------------------------------------------------------------
	//arg: item - serialized event to add to the queue [string]
	//return: true on success, false on failure [bool]

	function add($item) {
		global $kapenta;
	
		if ('' === trim($item)) { return false; }
		if ('' === $this->name) { return false; }

		$fileName = $this->queueDir . $kapenta->time() . '_' . $kapenta->utils->createUID() . '_.xml';

		$check = $kapenta->fs->put(fileName, $item, true, false); 
		if (false === $check) { return false; }

		$this->compact();
	}

	//----------------------------------------------------------------------------------------------
	//.	remove an item from the end of this queue
	//----------------------------------------------------------------------------------------------
	//returns: queue item or empty string if empty [string]

	function remove() {
		global $kapenta;

		$list = $kapenta->fs->listDir($this->queueDir);			//%	files in queue dir [array]
		$lastTime = -1;											//%	oldest timestamp [string]
		$lastFile = '';											//%	oldest file [string]				
		$item = '';												//%	contents of last item [string]

		foreach($list as $file) {
			$parts = explode('_', basename($file));
			$time = $parts[0];
			if ((-1 === $lastTime) || ($time < $lastTime)) {
				$lastTime = $time;
				$lastFile = $file;
			}
		}

		if ('' !== $lastFile) {
			$item = $kapenta->fs->get($lastFile);
			$kapenta->fs->delete($lastFile, true);
		}

		if (count($list) <= 1) {
			$this->uncompact();
		}
	
		return $item;
	}

	//----------------------------------------------------------------------------------------------
	//.	compress and store any overflowing items
	//----------------------------------------------------------------------------------------------

	function addOverflow() {
		global $kapenta;

		//	add any extra entries to the overflow

		$list = $kapenta->fs->listDir($this->queueDir);
		if (count($list) < $this->maxFiles) { return; }

		for ($i = 0; $i < $this->maxFiles; $i++) {
			array_pop($list);
		} 

		while(count($list) > 2) {
			$file1 = array_pop($list);
			$file2 = array_pop($list);
			gzipTwo($file1, $file2);			
		}

		$this->compactOverflow();

	}
	
	//----------------------------------------------------------------------------------------------
	//.	if here are more than $masFiles in overflow, join together the smallest two files
	//----------------------------------------------------------------------------------------------

	function compactOverflow() {
		//	add any extra entries to the overflow
		
		$continue = true;

		while (true = $continue) {
			$list = $kapenta->fs->listDir($this->overflowDir);
			if (count($list) < $this->maxFiles) { return; }

			$sized = array();

			for ($i = 0; $i < count($list); $i++) {
				$sized[$list[$i]] = $kapenta->fs->size($list[$i]);
			}

			asort($sized);

			$file1 = array_pop($sized);
			$file2 = array_pop($sized);

			$this->gzipTwo($file1, $file2);
		}
	}

	//-----------------	-----------------------------------------------------------------------------
	//.	pull the smallest outstanding file from the overflow directory and uncomress it
	//----------------------------------------------------------------------------------------------

	function uncompact() {
		global $kapenta;

		$list = $kapenta->fs->list($this->overflowDir);
		$smallestSize = -1;
		$smallestFile = '';

		for ($i = 0; $i < count($list); $i++) {
			
		} 

	}

	//-----------------	-----------------------------------------------------------------------------
	//.	zip two files together into the overflow directory
	//----------------------------------------------------------------------------------------------

	function gzipTwo($file1, $file2) {
		global $kapenta;

		$newFile = $this->overflowDir . $kapenta->time() . '_' . $kapenta->utils->createUID() . '.gz';

		$shellCmd = "zip $newFile $file1 $file2";

		$kapenta->fs->delete($file1);
		$kapenta->fs->delete($file2);
	}

	//----------------------------------------------------------------------------------------------
	//.	move a zip file from overflow and recursively unzip it
	//----------------------------------------------------------------------------------------------

	function unzip($zipFile) {
		global $kapenta;		

		//	copy zip file out of overflow directory and recursively unzip
		$newFile = str_replace('/overflow/', '/', $zipFile);
		$shellCmd = 'cp $zipFile'

		$continue = true;

		while (true == $continue) {
			$list = $kapenta->fs->listDir($this->queueDir, '.zip');

			if (0 === count($list)) { return; }

			for (i = 0; i < count($list), $i++) {
				$shellCmd = "cd " . $this->queueDir . " && unzip -j " . $list[i];
				$kapenta->fs->delete($list[i]);
			}
		}
	}

}

?>
