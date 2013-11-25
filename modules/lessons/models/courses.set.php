<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	collection object for courses
//--------------------------------------------------------------------------------------------------
//;	The purpose of this object is to collect information for all course manifests into a single
//;	index.dat.php file (native PHP serialization for efficiency)

class Lessons_Courses {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members = array();							//_	set of all course XML [array]
	var $loaded = false;							//_ set to true when loaded [bool]
	var $group = '';								//_	mediagroup
	var $dir = '/data/lessons/';					//_	data directory [string]
	var $fileName = '';								//_	static for now [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Lessons_Courses($group = '') {
		$this->group = trim($group);
		if ('' != $this->group) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load metadata
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $kapenta;
		global $session;

		if ('' == $this->group) { return false; }

		$this->fileName = 'data/lessons/' . $this->group . '.dat.php';

		if (false == $kapenta->fs->exists($this->fileName)) { $session->msg($this->rebuild()); }
		if (false == $kapenta->fs->exists($this->fileName)) { return false; }

		$serialized = $kapenta->fs->get($this->fileName);
		//echo "<textarea rows='10' cols='100'>$serialized</textarea>";
		$this->members = unserialize($serialized);

		//echo "<textarea rows='10' cols='100'>";
		//print_r($serialized);
		//echo "</textarea>";

		if (count($this->members) > 0) { $this->loaded = true; }

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save index.dat.php file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function save() {
		global $kapenta;

		if ('' == $this->group) { return false; }
		$this->fileName = 'data/lessons/' . $this->group . '.dat.php';
		return $kapenta->fs->put($this->fileName, serialize($this->members));
	}

	//----------------------------------------------------------------------------------------------
	//.	rebuild the index.dat.php file
	//----------------------------------------------------------------------------------------------
	//returns: html report [string]

	function rebuild() {
		global $kapenta;

		if ('' == $this->group) { return "Group not set before rebuilding."; }

		$this->members = array();
		$alphabetical = array();

		$report = "<h3>Rebuilding: " . $this->group  . "</h3>\n";

		if (false == $kapenta->fs->exists($this->dir)) {
			$installed = $kapenta->fileMakeSubdirs($this->dir . 'x.txt');
		}

		$report = '';												//%	return value [string]
		$subdirs = $kapenta->fileList($this->dir, '', true);		//% subdirectories [array]

		foreach($subdirs as $subdir) {
			if ('/' == substr($subdir, -1)) { $subdir = substr($subdir, 0, strlen($subdir) - 1); }
			$subdir = basename($subdir);

			$report .= "subdir: " . $subdir . "<br/>\n";
		
			$model = new Lessons_Course($subdir);
			if (true == $model->loaded) {
				if ($model->group == $this->group) {
					
					$model->makeStubs();

					$alphabetical[strtolower($model->title)] = $model->toArray();
					$report .= "Added: " . $model->title . " <br/>";

				}
			} else {
				$report .= "Could not load: $subdir <br/>";
			}
		}

		ksort($alphabetical);
		foreach($alphabetical as $member) { $this->members[$member['UID']] = $member; }
		$this->save();

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	list all groups
	//----------------------------------------------------------------------------------------------
	//opt: printReport - set to true to display a report of the scan [bool]
	//returns:	array of media groups	

	function listGroups($printReport = false) {
		global $kapenta;

		$groups = array();
		$report = "<h3>Listing groups: " . $this->group  . "</h3>\n";

		if (false == $kapenta->fs->exists($this->dir)) {
			$installed = $kapenta->fileMakeSubdirs($this->dir . 'x.txt');
		}

		$report = '';												//%	return value [string]
		$subdirs = $kapenta->fileList($this->dir, '', true);		//% subdirectories [array]

		foreach($subdirs as $subdir) {
			if ('/' == substr($subdir, -1)) { $subdir = substr($subdir, 0, strlen($subdir) - 1); }
			$subdir = basename($subdir);

			$report .= "subdir: " . $subdir . "<br/>\n";
		
			$model = new Lessons_Course($subdir);
			if (true == $model->loaded) {
				if (false == in_array($model->group, $groups)) {
					$groups[] = $model->group;
					$report .= "noted group: " . $model->group . "<br/>";
				}
				$report . "course: " . $model->title . " (" . $model->group . ")<br/>";
			} else {
				$report .= "Could not load: $subdir <br/>";
			}
		}

		if (true == $printReport) { echo $report; }

		return $groups;		
	}

	//----------------------------------------------------------------------------------------------
	//	make a dat6abase copy of each course and document (collection and stub)
	//----------------------------------------------------------------------------------------------
	//returns: HTML report of import process [string]

	function importAll() {
		global $kapenta;

		$groups = array();
		$report = "<h3>Importing courses into local database: " . $this->group  . "</h3>\n";

		$report .= $this->clearDbCache();

		if (false == $kapenta->fs->exists($this->dir)) {
			$installed = $kapenta->fileMakeSubdirs($this->dir . 'x.txt');
		}

		$report = '';												//%	return value [string]
		$subdirs = $kapenta->fileList($this->dir, '', true);		//% subdirectories [array]

		foreach($subdirs as $subdir) {
			if ('/' == substr($subdir, -1)) { $subdir = substr($subdir, 0, strlen($subdir) - 1); }
			$subdir = basename($subdir);

			$report .= "subdir: " . $subdir . "<br/>\n";
		
			$model = new Lessons_Course($subdir);
			if (true == $model->loaded) {
				$report .= "Importing: " . $model->title . " (" . $model->group . ")<br/>";
				$report .= $this->importSingle($subdir);
			} else {
				$report .= "Could not load: $subdir <br/>";
			}
		}

		if (true == $printReport) { echo $report; }

		return $report;		
	}

	//----------------------------------------------------------------------------------------------
	//	import a single course
	//----------------------------------------------------------------------------------------------

	function importSingle($uid) {
		$diskmodel = new Lessons_Course($uid);
		$dbmodel = new Lessons_Collection();

		$dbmodel->loadArray($diskmodel->toArray());
		$dbmodel->save();

		$report .= "Importing course: " . $diskmodel->title . "<br/>\n";

		foreach($diskmodel->documents as $document) {
			$stub = new Lessons_Stub();

			foreach($document as $key => $value) {
				switch($key) {

					case 'UID':				$stub->UID = $value;			break;
					case 'course':			$stub->course = $value;			break;
					case 'document':		$stub->document = $value;		break;
					case 'title':			$stub->title = $value;			break;
					case 'description':		$stub->description = $value;	break;
					case 'cover':			$stub->cover = $value;			break;
					case 'thumb':			$stub->file = $value;			break;
					case 'attribname':		$stub->attribname = $value;		break;
					case 'attribuurl':		$stub->attriburl = $value;		break;
					case 'licencename':		$stub->licencename = $value;	break;
					case 'licenceurl':		$stub->lincenceurl = $value;	break;
					case 'downloadfrom':	$stub->downloadfrom = $value;	break;
					case 'createdOn':		$stub->createdOn = $value;		break;
					case 'createdBy':		$stub->createdBy = $value;		break;
					case 'editedOn':		$stub->editedOn = $value;		break;
					case 'editedBy':		$stub->editedBy = $value;		break;
					case 'shared':			$stub->shared = 'no';			break;
					case 'alias':			$stub->alias = $value;			break;

				}
			}

			$report .= "Adding document: " . $stub->title . " (" . $stub->UID . ")<br/>\n";
			$stub->save();
		}
	
		return $report;
	}

	//---------------------------------------------------------------------------------------------
	//.	clear caches collection and stub objects from the database
	//---------------------------------------------------------------------------------------------
	//returnns: HTML report of actions taken

	function clearDbCache() {
		global $db;

		$report = '<b>Clearing db cache:</b><br/>';					//%	return value [string]

		$range = $db->loadRange('lessons_collection', '*');
		foreach($range as $item) {
			$report .= "Uncaching course/collection: " . $item['title'] . " (" . $item['UID'] . ")<br/>\n";
			//simple deletion since these cache objects are never shared
			$sql = "delete from `lessons_collection` where UID='" . $item['UID'] . "'";
			$report .= "$sql<br/>\n";
		}

		$range = $db->loadRange('lessons_stub', '*');
		foreach($range as $item) {
			$report .= "Uncaching stub: " . $item['title'] . " (" . $item['UID'] . ")<br/>";
			//simple deletion since these cache objects are never shared
			$sql = "delete from `lessons_stub` where UID='" . $item['UID'] . "'";
			$report .= "$sql<br/>\n"; 
		}

		return $report;
	}

}

?>
