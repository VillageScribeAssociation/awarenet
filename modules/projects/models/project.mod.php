<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for managing projects
//--------------------------------------------------------------------------------------------------
//+	Projects are a special type of document co-created by several users.  All users who are members
//+	of the project have edit permissions on it and can edit text, add images, etc.
//+	A projects states may be 'open', 'finished' or 'cancelled'.  The user who started a project
//+	may add members and can set the project's status.
//+
//+	On completion, a notification is generated for friends and classmates of all members.
//+	Members can edit the abstract and content of the project but only the user who started the
//+	project may edit the title.  The content of a project is divided into 'sections' which are 
//+	edited independantly.  Same as wiki articles.
//+
//+	structure of content:
//+  <article>
//+	   <section>
//+		<UID>1239877129</UID>
//+	    <title>Microfauna</title>
//+	    <weight>3</weight>
//+	    <content><![CDATA[This is escaped HTML]]></content>
//+	  </section>
//+  </article>

class Projects_Project {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;						//_	currently loaded database record [array]
	var $dbSchema;					//_	database table definition [array]
	var $loaded;					//_	set to true when an object has been loaded [bool]

	var $UID;						//_ UID [string]
	var $title;						//_ title [string]
	var $abstract;					//_ wyswyg [string]
	var $content;					//_ plaintext [string]
	var $status;					//_ varchar(10) [string]
	var $finishedOn;				//_ datetime [string]
	var $createdOn;					//_ datetime [string]
	var $createdBy;					//_ ref:Users_User [string]
	var $editedOn;					//_ datetime [string]
	var $editedBy;					//_ ref:Users_User [string]
	var $alias;						//_ alias [string]

	var $sections;					//_	the sections of this document [array]
	var $sectionsLoaded = false;	//_	set to true when page sections have been loaded [bool]

	var $members;					//_	range of serialized Projects_Membership objects [array]
	var $membersLoaded = false;		//_	set to true when members have been loaded [bool]

	var $sectionFields = 'UID|title|weight|content';
	var $allowTags = '<img><b><i><a><table><tr><td><th><ul><ol><li><span><p><br><font><h1><h2><h3><blockquote><pre>';

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Project object [string]

	function Projects_Project($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Project ' . $this->UID;		// set default title
			$this->status = 'open';
			$this->sections = array();
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Project object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Project object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		//if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->abstract = $ary['abstract'];
		$this->content = $ary['content'];
		$this->status = $ary['status'];
		$this->finishedOn = $ary['finishedOn'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->expandSections($this->content);
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }

		// ensure that the user who started project is a member
		$this->addMember($this->createdBy, 'admin');

		$this->alias = $aliases->create('projects', 'Projects_Project', $this->UID, $this->title);
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
		$dbSchema['model'] = 'Projects_Project';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'abstract' => 'TEXT',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'finishedOn' => 'DATETIME',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'title',
			'abstract',
			'content',
			'status',
			'finishedOn',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy',
			'alias'
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
			'title' => $this->title,
			'abstract' => $this->abstract,
			'content' => $this->collapseSections(),
			'status' => $this->status,
			'finishedOn' => $this->finishedOn,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user, $theme;
		$ary = $this->toArray();

		$ary['editUrl'] = '';			$ary['editLink'] = '';
		$ary['viewUrl'] = '';			$ary['viewLink'] = '';
		$ary['delUrl'] = '';			$ary['delLink'] = '';
		$ary['newUrl'] = '';			$ary['newLink'] = '';
		$ary['editAbstractUrl'] = '';	$ary['editAbstractLink'] = '';

		//------------------------------------------------------------------------------------------
		//	permissions
		//------------------------------------------------------------------------------------------

		$editAuth = false;
		if ( (true == $user->authHas('projects', 'Projects_Project', 'edit', $this->UID)) 
			 AND (true == $this->isMember($user->UID)) ) { $editAuth = true; }

		$delAuth = false;
		if ('admin' == $user->role) { $delAuth = true; }
		// TODO: decide whether project admins can delete a project
		// TODO: check full range of permissions

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('projects', 'Projects_Project', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%projects/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($editAuth == true) {
			$ary['editUrl'] =  '%%serverPath%%projects/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 

			$ary['editAbstractUrl'] = '%%serverPath%%projects/editabstract/'
									. $this->alias;

			$ary['editAbstractLink'] = "<a href='" . $ary['editAbstractUrl'] . "'>"
									. "[edit abstract]</a>"; 
		}

		if ($delAuth == true) {
			$ary['delUrl'] =  '%%serverPath%%projects/confirmdelete/UID_'. $this->UID .'/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if ($user->authHas('projects', 'Projects_Project', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%projects/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new project]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	abstract 
		//------------------------------------------------------------------------------------------

		$ary['abstractHtml'] = str_replace(">\n", ">", $ary['abstract']);
		$ary['abstractHtml'] = str_replace("\n", "<br/>\n", $ary['abstractHtml']);
	
		$ary['summary'] = $theme->makeSummary($ary['abstract'], 400);

		//------------------------------------------------------------------------------------------
		//	byline
		//------------------------------------------------------------------------------------------

		$ary['byline'] = array();
		$members = $this->getMembers();
		foreach($members as $userUID => $role) 
			{ $ary['byline'][] = "[[:users::namelink::userUID=" . $userUID . ":]]"; }
		$ary['byline'] = implode(", \n", $ary['byline']);

		//------------------------------------------------------------------------------------------
		//	index
		//------------------------------------------------------------------------------------------

		$ary['indexHtml'] = ''; 
		foreach($this->sections as $UID => $section) {
			$link = "%%serverPath%%projects/" . $ary['alias'] . "#s" . $section['UID'];
			$title = $section['weight'] . ". " . $section['title'];
			$ary['indexHtml'] .= "<a href='" . $link . "'>" . $title . "</a><br/>\n";
		}

		//------------------------------------------------------------------------------------------
		//	content
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = '';
		foreach($this->sections as $UID => $section) {
		
			$secEditUrl = '';
			$secEditLink = '';
			if ($editAuth == true) {
				$secEditUrl = '%%serverPath%%projects/editsection/'
							. 'section_' . $section['UID'] . '/' . $ary['alias'];
				$secEditLink = "<a href='" . $secEditUrl . "'>[edit section]</a>";
			}

			$ary['contentHtml'] .= "<h3><a name='s" . $section['UID'] . "'>"
								 . $section['title'] . "</a></h3>\n"
								 . $section['content'] . $secEditLink . "\n";
		}

		//------------------------------------------------------------------------------------------
		//	abstract marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		if ($ary['abstract'] == '') { $ary['abstract'] = ' '; }
		$ary['projectTitle'] = $ary['title'];
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	make extended array of section data
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a section [string]
	//returns: associative array of section properties [array]

	function sectionArray($sectionUID) {
		if (array_key_exists($sectionUID, $this->sections) == false) { return false; }
		$ary = $this->sections[$sectionUID];
		$ary['projectUID'] = $this->UID;
		$ary['projectTitle'] = $this->title;
		$ary['sectionTitle'] = $ary['title'];	
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
	//.	determine if a given user is an admin or member of a project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if the specified user has edit permissions on this project, else false [bool]

	function hasEditAuth($userUID) {
		global $user;
		//TODO: remove, use $user->authHas(...)
		if ('admin' == $user->role) { return true; }

		$members = $this->getMembers();
		foreach($members as $mUID => $role) {
			if (($userUID == $mUID) && (($role == 'admin')||($role == 'member'))) { return true; } 
		}
		return false;
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

	//==============================================================================================
	//	REVISIONS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	save a revision
	//----------------------------------------------------------------------------------------------
	//returns: empty string on succes, html report on failure [string]

	function saveRevision() {
		$revision = new Projects_Revision();
		$revision->projectUID = $this->UID;
		$revision->title = $this->title;
		$revision->abstract = $this->abstract;
		$revision->content = $this->collapseSections();
		$report = $revision->save();
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	get URL of this project (deprecated) //TODO: find out if anything still uses this
	//----------------------------------------------------------------------------------------------
	//returns: URL of current project [string]

	function getUrl() { return "%%serverPath%%projects/" . $this->alias; }

	//----------------------------------------------------------------------------------------------
	//.	make an link to this project (deprecated) //TODO: find out if anything still uses this
	//----------------------------------------------------------------------------------------------
	//returns: an html anchor tag [string]

	function getLink() { return "<a href='". $this->getUrl() ."'>". $this->title ."</a>";}

	//==============================================================================================
	//	MEMBERSHIPS	
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load a memberships of this project into $this->members
	//----------------------------------------------------------------------------------------------

	function loadMembers() {
		global $db;
		$conditions = array("projectUID='" . $db->addMarkup($this->UID) . "'");
		$range = $db->loadRange('Projects_Membership', '*', $conditions);
		$this->members = $range;
		$this->membersLoaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a member to the project, or change the role of an existing member
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: role - role in the project (admin|member) [string]
	//returns: true on success, false on failure [bool]

	function addMember($userUID, $role) {
		global $db;
		if (false == $db->objectExists('Users_User', $userUID)) { return false; }
		if (false == $this->membersLoaded) { $this->loadMembers(); }

		//------------------------------------------------------------------------------------------
		//	check user's role in the project if an existing member
		//------------------------------------------------------------------------------------------
		foreach($this->members as $membership) {
			if ($userUID == $membership['userUID']) {
				$model = new Projects_Membership();
				$model->loadArray($membership);
				$model->role = $role;
				$model->joined = $db->datetime();
				$check = $model->save();
				$this->loadMembers();					// reload memberships
				if ('' != $check) { return false; }
				return true;
			}
		}

		//------------------------------------------------------------------------------------------	
		//	create membership if one does not already exist
		//------------------------------------------------------------------------------------------	
		$model = new Projects_Membership();
		$model->projectUID = $this->UID;
		$model->userUID = $userUID;
		$model->role = $role;
		$model->joined = $db->datetime();
		$model->save();
		$check = $model->save();
		$this->loadMembers();							// reload memberships
		if ('' != $check) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a member from the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user to remove from the project [string]
	//returns: true on success, false on failure [bool]

	function removeMember($userUID) {
		global $session;
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		if (false == $this->isMember($userUID)) {
			$session->msg('User ' . $userUID . ' is not a member of this project.', 'bad');
			return false;
		}
		foreach($this->members as $membership) {
			if ($userUID == $membership['userUID']) {
				$model = new Projects_Membership();
				$model->loadArray($membership);
				$check = $model->delete();
				$this->loadMembers();					// reload memberships
				if (true == $check) { return true; }
			}
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user (UID) is a member of the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: membership record if a member, false if not [array][bool]

	function isMember($userUID) {
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		foreach($this->members as $membership) 
			{ if ($userUID == $membership['userUID']) { return true; } }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user (UID) is a member of the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: membership record if a member, false if not [array][bool]

	function isAdmin($userUID) {
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		foreach($this->members as $msp) 
			{ if (($userUID == $msp['userUID']) && ('admin' == $msp['role'])) { return true; } }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get all members of a project (exc. prospective), returns array of [userUID] => [role]
	//----------------------------------------------------------------------------------------------
	//opt: prospective - is true them embers who have asked are included [bool]
	//returns: array of memberships (userUID => role) [array]

	function getMembers() {
		$retVal = array();													//% return value [array]
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		foreach($this->members as $msp) 
			{ if ('asked' != $msp['role']) { $retVal[$msp['userUID']] = $msp['role']; } }

		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//.	get prospective members of a project, returns array of [userUID] => [role] 
	//----------------------------------------------------------------------------------------------
	//returns: array of membership applications (userUID => role) [array]

	function getProspectiveMembers() {	//TODO: remove this entirely
		$retVal = array();													//% return value [array]
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		foreach($this->members as $msp) 
			{ if ('asked' == $msp['role']) { $retVal[$msp['userUID']] = $msp['role']; } }
		return $retVal;
	}

}

?>
