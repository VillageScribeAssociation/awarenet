<?

//--------------------------------------------------------------------------------------------------
//*	object to represent project revisions
//--------------------------------------------------------------------------------------------------
//+	since the projects module is derived from the wiki module, changes to wiki revisions model
//+	should be copied here.

class Projects_Revision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $projectUID;		//_ varchar(33) [string]
	var $title;				//_ title [string]
	var $abstract;			//_ wyswyg [string]
	var $content;			//_ plaintext [string]
	var $reason;			//_ varchar(255) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	var $sections;			//_	array of section objects [array]
	var $sectionsLoaded = false;
	var $sectionFields = 'UID|title|weight|content';

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Revision object [string]

	function Projects_Revision($UID = '') {
		global $db;
		$this->sections = array();
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Revision ' . $this->UID;	// set default title
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Revision object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Revision object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->projectUID = $ary['projectUID'];
		$this->title = $ary['title'];
		$this->abstract = $ary['abstract'];
		$this->content = $ary['content'];
		$this->reason = $ary['reason'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
	
		$this->expandSections($this->content);

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'projects';
		$dbSchema['model'] = 'projects_revision';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'projectUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'abstract' => 'TEXT',
			'content' => 'TEXT',
			'reason' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'projectUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be not kept for any fields
		$dbSchema['nodiff'] = array(
			'UID', 'projectUID', 'title', 'abstract', 'content', 'reason',
			'createdOn', 'createdBy', 'editedOn', 'editedBy'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'projectUID' => $this->projectUID,
			'title' => $this->title,
			'abstract' => $this->abstract,
			'content' => $this->content,
			'reason' => $this->reason,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ary = $this->toArray();

		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	load as project and convert to HTML
		//------------------------------------------------------------------------------------------		

		$model = new Projects_Project($ary['projectUID']);

		$ary['alias'] = $model->alias;
		$ary['status'] = $model->status;
		$ary['finishedOn'] = $model->finishedOn;

		$model->loadArray($ary);
		$ary = $model->extArray();

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('projects', 'projects_revision', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%projects/showrevision/' . $ary['UID'];
			$ary['viewLink'] = "<a href='%%serverPath%%projects/showrevision/" . $ary['UID'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}	// TODO: action to view a single revision

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------
		$ary['editedOnLong'] = date('jS F, Y', strtotime($ary['editedOn']));

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//==============================================================================================
	//	SECTIONS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	expand sections
	//----------------------------------------------------------------------------------------------
	//returns: number of sections [int]

	function expandSections($xml) {
		global $kapenta;
		$this->sections = array();
		if ('' == $xml) { return 0; }

		$sF = explode('|', $this->sectionFields);
		$doc = new KXmlDocument($xml);					// 
		$secIds = $doc->getChildren(1);					// children of root node
		if (false === $secIds) { return 0; }

		foreach($secIds as $index => $sectionId) {
			$nsIds = $doc->getChildren($sectionId);
			//print_r($nsIds);

			$section = array(
				'UID' => $kapenta->createUID(),
				'title' => 'New Section',
				'weight' => '0',
				'content' => ''
			);

			foreach($nsIds as $nsId) {
				$ent = $doc->getEntity($nsId);
				//print_r($ent);
				switch($ent['type']) {
					case "UID":			$section['UID'] = $ent['value']; 			break;
					case "title":		$section['title'] = $ent['value']; 			break;
					case "weight":		$section['weight'] = $ent['value']; 		break;

					case "content":
						$section['content'] = $doc->stripCDATA($ent['value']);
						break;
				}
			}

			$this->sections[$section['UID']] = $section;
			//TODO: use KXmlDocument to fill array
		}
	
		$this->sectionsLoaded = true;
		return count($this->sections);		
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse sections
	//----------------------------------------------------------------------------------------------

	function collapseSections() {
		global $utils;
		//$sF = explode('|', $this->sectionFields);
		$xml = '';		

		foreach($this->sections as $section) {
			if (false == array_key_exists('content', $section)) { $section['content'] = ''; }
			$section['content'] = '<![CDATA[[' . $section['content'] . ']]>';
			$xml .= $utils->arrayToXml2d('section', $section);
		}

		$xml = "<article>\n$xml</article>";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	sort sections by weight
	//----------------------------------------------------------------------------------------------

	function sort() {
		$twoD = array();
		$sections = $this->sections;
		foreach($sections as $key => $section) { $twoD[$key] = $section['weight']; 	}

		asort($twoD);
		$this->sections = array();
		foreach($twoD as $key => $weight) {	$this->sections[$key] = $sections[$key]; }
	}

	//----------------------------------------------------------------------------------------------
	//.	add a section
	//----------------------------------------------------------------------------------------------
	//arg: title - section title [string]
	//arg: weight - weight of this section [int]

	function addSection($title, $weight) {
		global $kapenta; 
		$newSection = array();
		$newSection['UID'] = $kapenta->createUID();
		$newSection['title'] = $title;
		$newSection['weight'] = $weight;
		$newSection['content'] = '';
		$this->sections[$newSection['UID']] = $newSection;
		$this->sort();
		$this->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a section
	//----------------------------------------------------------------------------------------------
	//arg: rmUID - UID of the section to be removed [string]
	//returns: true if the section was deleted, false if not found [bool]

	function deleteSection($rmUID) {
		$newSections = array();
		$found = false;
		foreach($this->sections as $UID => $section) {
			if ($UID == $rmUID) {
				$found = true;
			} else {
				$newSections[$UID] = $section;
				if (true == $found) { $newSections[$UID]['weight']--; }
			}
		}
		$this->sections = $newSections;
		if (true == $found) { $this->save(); }
		return $found;
	}

	//----------------------------------------------------------------------------------------------
	//.	find the section with the largest weight
	//----------------------------------------------------------------------------------------------
	//returns: weight of the heaviest section [int]

	function getMaxWeight() {
		$maxWeight = 0;
		foreach($this->sections as $sUID => $section) {
			if ($section['weight'] > $maxWeight) { $maxWeight = $section['weight']; }		
		}
		return $maxWeight;
	}

	//----------------------------------------------------------------------------------------------
	//.	increase a section's weight
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a section [string]
	//returns: true on success, false if unchanged [bool]

	function incrementSection($sectionUID) {
		if (false == array_key_exists($sectionUID, $this->sections)) { return false; }
		$oldWeight = $this->sections[$sectionUID]['weight'];		// this section's weight

		if ($this->getMaxWeight() == $oldWeight) { return false; }	// already max weight

		foreach($this->sections as $currUID => $section) {			// dec section below
			if (($oldWeight + 1) == $section['weight']) 
				{ $this->sections[$currUID]['weight'] = $oldWeight; }
		}

		$this->sections[$sectionUID]['weight'] = $oldWeight + 1;	// inc self
		$this->sort();
		$this->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	decrease a section's weight
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a section [string]
	//returns: true if decremented, false if not [bool]

	function decrementSection($sectionUID) {
		if (array_key_exists($sectionUID, $this->sections) == false) { return false; }
		$oldWeight = $this->sections[$sectionUID]['weight'];		// this section's weight
		if (1 == $oldWeight) { return false; }

		foreach($this->sections as $currUID => $section) {			// inc section above
			if (($oldWeight - 1) == $section['weight']) 
				{ $this->sections[$currUID]['weight'] = $oldWeight; }
		}

		$this->sections[$sectionUID]['weight'] = $oldWeight - 1;	// inc self
		$this->sort();
		$this->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert to html for diff
	//----------------------------------------------------------------------------------------------
	//returns: project title, abstract and sections compiled into html [string]

	function getSimpleHtml() {
		$html = "<h1>" . $this->title . "</h1>" . $this->abstract;
		foreach($this->sections as $key => $section) {
			$html .= "<h2>" . trim($section['title']) . "</h2>" . trim($section['content'])
				  . "<p><small>weight: " . $section['weight'] . " uid: " . $section['UID']
				  . " title: " . $section['title'] . "</small></p>";
		}
		return $html;
	}


}

?>
