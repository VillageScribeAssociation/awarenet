<?php

	require_once($kapenta->installPath . 'modules/lessons/models/collection.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');

//--------------------------------------------------------------------------------------------------
//*	Represents an installable course package
//--------------------------------------------------------------------------------------------------
//
//	NOTE: individual items (videos, files, etc) are represented as arrays like so:
//
//		UID			-	UID of lesson / element
//		title		-	name of this lesson / element
//		description -	html
//		file		-	in /data/lessions/
//		thumb		-	100x100 file in /data/lessons/
//		thumb300	-	300x{variable}
//		thumb50		-	50x50 
//		url			-	source URL to download this asset from, if possible
//		licence		-	licence of item
//		attrib		-	attribution text
//		attribUrl	-	attribution URL

class Lessons_CourseXml {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $UID = '';				//_	UID of this course [string]
	var $language = 'en';		//_	language of this course (en|af|xh) [string]
	var $group = '';			//_	textbooks|videos [string]
	var $subject = '';			//_	school subject [string]
	var $grade = '';			//_	grade level [string]
	var $title = '';			//_	title of this course [string]
	var $description = '';		//_	description of this course [string]
	var $documents = array();	//_	set of document arrays [array:array:string]
	var $fileName = '';			//_	location of manifest file [string]
	var $loaded = false;		//_	set to true when manifest loaded [bool]

	var $importDir = 'data/lessons/';

	var $dProperties = array();	//_	XML fields of documents [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an installed course [string]

	function Lessons_CourseXml($UID = '') {
		global $kapenta;

		$this->dProperties = array(
			'uid' => 'mandatory',
			'type' => 'mandatory',
			'title' => '',
			'description' => '',
			'cover' => '',
			'thumb' => '',
			'file' => '',
			'attribname' => '',
			'attriburl' => '',
			'licencename' => '',
			'licenceurl' => '',
			'downloadfrom' => ''
		);

		$this->UID = $kapenta->createUID();
		if ('' != $UID) { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load from xml serialized file on disk
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a course [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		global $session;

		if ('' == $UID) { return false; }

		$UID = str_replace('.', '', $UID);			//	directory traversal  TODO: improve
		$UID = str_replace('/', '', $UID);			//	""
		$UID = str_replace('\\', '', $UID);			//	""

		$this->loaded = false;
		$this->fileName = $this->importDir . $UID . '/manifest.xml';

		if (false == $kapenta->fs->exists('data/lessons/')) {
			$kapenta->fileMakeSubdirs('/data/lessons/x.txt');
		}

		if (false == $kapenta->fs->exists($this->fileName)) { return false; }

		$xd = new KXmlDocument($this->fileName, true);

		if (false == $xd->loaded) {
			$kapenta->session->msg("could not load: " . $this->fileName);
			return false; 
		}

		$root = $xd->getEntity(1);
		if ('course' != $root['type']) { return false; }

		foreach($root['children'] as $childId) {
			$child = $xd->getEntity($childId);
			switch($child['type']) {
				case 'uid':			$this->UID = $child['value'];			break;
				case 'title':		$this->title = $child['value'];			break;
				case 'subject':		$this->subject = $child['value'];		break;
				case 'grade':		$this->grade = $child['value'];			break;
				case 'description':	$this->description = $child['value'];	break;
				case 'group':		$this->group = $child['value'];			break;
				case 'language':	$this->language = $child['value'];		break;
				//TODO: more optional elements here

				case 'documents':
					foreach($child['children'] as $docId) {
						$doc = $xd->getEntity($docId);
						$newDoc = array();

						foreach($this->dProperties as $item => $v) { $newDoc[$item] = ''; }

						foreach($doc['children'] as $propertyId) {
							$property = $xd->getEntity($propertyId);
							$newDoc[$property['type']] = $property['value'];
						}
						if (false == array_key_exists('uid', $newDoc)) {
							$newDoc['uid'] = $kapenta->createUID();
						}
					
						if (true == array_key_exists('title', $newDoc)) {
							$this->documents[$newDoc['uid']] = $newDoc;
						}
					}
					break;		//..................................................................

			}
		}

		if ('videolesson' == $this->group) { $this->group = 'videolessons'; $this->save(); }

		if ('' != $this->title) { $this->loaded = true; }
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to XML and save to disk
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function save() {
		global $kapenta;
		if ('' == trim($this->title)) { return false; }
		if ('' == trim($this->language)) { return false; }
		if ('' == trim($this->UID)) { return false; }

		$this->fileName = 'data/lessons/' . $this->UID . '/manifest.xml';

		$kapenta->fileMakeSubdirs($this->fileName);
		$check = $kapenta->fs->put($this->fileName, $this->toXml());
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//*	save to database
	//----------------------------------------------------------------------------------------------

	function saveDb() {
		$model = new Lessons_Collection($this->UID);

		$model->UID = $this->UID;
		$model->title = $this->title;
		$model->description = $this->description;
		$model->subject = $this->subject;
		$model->grade = $this->grade;
		$model->mediagroup = $this->group;
		$model->language = $this->language;

		return $model->save();
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if this course includes a given document
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID fo a document [string]

	function has($UID) {
		foreach($this->documents as $doc) {
			if ($UID == $doc['uid']) { return true; }
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to array for views
	//----------------------------------------------------------------------------------------------

	function toArray() {
		$ary = array(
			'UID' => $this->UID,
			'title' => $this->title,
			'fileName' => $this->fileName,
			'language' => $this->language,
			'subject' => $this->subject,
			'grade' => $this->grade,
			'description' => $this->description,
			'group' => $this->group
		);
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	rebuild stub set for this course's documents
	//----------------------------------------------------------------------------------------------

	function makeStubs() {
		global $kapenta;
		global $session;

		foreach($this->documents as $document) {
			if (
				(true == array_key_exists('file', $document)) &&
				(true == $kapenta->fs->exists($document['file']))
			) {
				
				$model = new Lessons_Stub();
				$model->loadArray($document);
				$model->UID = $document['uid'];
				$report = $model->save();
				if ('' !== $report) {
					$kapenta->session->msg('Could not save Lessons_Stub::' . $model->UID);
				}

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to XML
	//----------------------------------------------------------------------------------------------
	//returns: XML representation of this object [string]

	function toXml() {

		$documents = '';

		foreach($this->documents as $document) {
			$documents .= "\t\t<document>\n";
			foreach($document as $key => $value) {
				$documents .= "\t\t\t<$key>$value</$key>\n";
			}
			$documents .= "\t\t</document>\n";
		}

		$xml = ''
		 . "<course>\n"
		 . "\t<uid>" . $this->UID . "</uid>\n"
		 . "\t<title>" . $this->title . "</title>\n"
		 . "\t<description>" . $this->description . "</description>\n"
		 . "\t<language>" . $this->language . "</language>\n"
		 . "\t<subject>" . $this->subject . "</subject>\n"
		 . "\t<grade>" . $this->grade . "</grade>\n"
		 . "\t<group>" . $this->group . "</group>\n"
		 . "\t<documents>\n"
		 . $documents
		 . "\t</documents>\n"
		 . "</course>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	list all courses on disk
	//----------------------------------------------------------------------------------------------
	//returns: array of course UIDs [array:string]

	function listAll() {
		global $kapenta;
		$list = array();			//%	return value [string]	

		$dirs = $kapenta->fs->listDir($this->importDir, '', true);
		
		//	remove cache and scraper dirs
		foreach($dirs as $dir) {
			$dir = str_replace($this->importDir, '', $dir);
			$dir = str_replace('/', '', $dir);
			if (('library' !== $dir) && ('scraper' !== $dir)) {
				$list[] = $dir;
			}
		}

		return $list;		
	}

}

?>
