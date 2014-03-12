<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/memberships.set.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');

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
	var $membership;				//_ May be (open|closed) (varchar(10)) [string]
	var $finishedOn;				//_ datetime [string]
	var $createdOn;					//_ datetime [string]
	var $createdBy;					//_ ref:Users_User [string]
	var $editedOn;					//_ datetime [string]
	var $editedBy;					//_ ref:Users_User [string]
	var $alias;						//_ alias [string]

	var $sections;					//_	object:Projects_Sections - parts of this document [object]
	var $memberships;				//_	object:Projects_Memberships - collection [object]

	var $sectionFields = 'UID|title|weight|content';

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Project object [string]

	function Projects_Project($raUID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				//	initialise table schema
		$this->sections = new Projects_Sections();			//	sections in this project
		$this->memberships = new Projects_Memberships();	//	sections in this project

		if ('' != $raUID) { $this->load($raUID); }			//	try load an object from the database
		if (false == $this->loaded) {						//	check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	//	make new object
			$this->loadArray($this->data);					//	initialize
			$this->title = 'New Project ' . $this->UID;		//	set default title
			$this->status = 'open';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Project object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $kapenta;
		$objary = $kapenta->db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Project object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		global $session;
		//if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }

		if (false == array_key_exists('abstract', $ary)) { 
			//$kapenta->session->msg('Missing abstract on project load.', 'bad');
			$ary['abstract'] = '';
		}

		if (false == array_key_exists('membership', $ary)) { 
			//$kapenta->session->msg('Missing membership on project load.', 'bad');
			$ary['membership'] = '';
		}


		if (false == array_key_exists('content', $ary)) { 
			//$kapenta->session->msg('Missing membership on project load.', 'bad');
			$ary['content'] = '';
		}

		//------------------------------------------------------------------------------------------
		//	own properties
		//------------------------------------------------------------------------------------------

		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->abstract = $ary['abstract'];
		$this->content = $ary['content'];
		$this->status = $ary['status'];
		$this->membership = $ary['membership'];
		$this->finishedOn = $ary['finishedOn'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];

		//------------------------------------------------------------------------------------------
		//	child objects
		//------------------------------------------------------------------------------------------
		$this->sections = new Projects_Sections($this->UID);		// initlializes lazily
		$this->memberships = new Projects_Memberships($this->UID);	// initlializes lazily

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }

		// ensure that the user who started project is a member
		// $this->memberships->add($this->createdBy, 'admin');

		$this->alias = $aliases->create('projects', 'projects_project', $this->UID, $this->title);
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';

		if ('' == $this->status) { $this->status = 'open'; }

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
		$dbSchema['model'] = 'projects_project';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'abstract' => 'TEXT',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'membership' => 'VARCHAR(10)',
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
			'membership',
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
			'status' => $this->status,
			'membership' => $this->status,
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
		global $kapenta;
		global $kapenta;
		global $theme;

		$ary = $this->toArray();

		$ary['editUrl'] = '';			$ary['editLink'] = '';
		$ary['viewUrl'] = '';			$ary['viewLink'] = '';
		$ary['delUrl'] = '';			$ary['delLink'] = '';
		$ary['newUrl'] = '';			$ary['newLink'] = '';
		$ary['editAbstractUrl'] = '';	$ary['editAbstractLink'] = '';
		$ary['nameLink'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	permissions
		//------------------------------------------------------------------------------------------

		$editAuth = false;
		if (true == $kapenta->user->authHas('projects', 'projects_project', 'editmembers', $this->UID))  {
			$editAuth = true;
		}

		$delAuth = false;
		if ('admin' == $kapenta->user->role) { $delAuth = true; }
		// TODO: decide whether project admins can delete a project
		// TODO: check full range of permissions

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $kapenta->user->authHas('projects', 'projects_project', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%projects/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>";
			$ary['nameLink'] = "<a href='" . $ary['viewUrl'] . "'>" . $ary['title'] . "</a>";  
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
		
		if ($kapenta->user->authHas('projects', 'projects_project', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%projects/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new project]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	abstract 
		//------------------------------------------------------------------------------------------

		$ary['createdOnLong'] = $kapenta->longDatetime($ary['createdOn']);
		$ary['editedOnLong'] = $kapenta->longDatetime($ary['editedOn']);

		$ary['abstractHtml'] = str_replace(">\n", ">", $ary['abstract']);
		$ary['abstractHtml'] = str_replace("\n", "<br/>\n", $ary['abstractHtml']);
	
		$ary['summary'] = $theme->makeSummary($ary['abstract'], 400);

		//------------------------------------------------------------------------------------------
		//	byline
		//------------------------------------------------------------------------------------------

		/*
		$ary['byline'] = array();
		$members = $this->memberships->getMembers();
		foreach($members as $userUID => $role) 
			{ $ary['byline'][] = "[[:users::namelink::userUID=" . $userUID . ":]]"; }
		$ary['byline'] = implode(", \n", $ary['byline']);
		*/

		$ary['byline'] = '';

		//------------------------------------------------------------------------------------------
		//	index
		//------------------------------------------------------------------------------------------
		if (false == $this->sections->loaded) { $this->sections->load(); }

		$ary['indexHtml'] = ''; 
		foreach($this->sections->members as $UID => $section) {
			$link = "%%serverPath%%projects/" . $ary['alias'] . "#s" . $section['UID'];
			$title = $section['weight'] . ". " . $section['title'];
			$ary['indexHtml'] .= "<a href='" . $link . "'>" . $title . "</a><br/>\n";
		}

		//------------------------------------------------------------------------------------------
		//	content
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = '[[:projects::allsections::projectUID=' . $this->UID . ':]]';
				
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
		if (false == $this->sections->has($sectionUID)) { return false; }
		$ary = $this->sections->members[$sectionUID];
		$ary['projectUID'] = $this->UID;
		$ary['projectTitle'] = $this->title;
		$ary['sectionTitle'] = $ary['title'];
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//==============================================================================================
	//	SECTIONS
	//==============================================================================================
	//TODO: move most of these functions and call to them into the sections.set.php object and
	// instantiate it on this, as $this->sections.

	//----------------------------------------------------------------------------------------------
	//.	discover if a section exists in this set
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Sections_Section object [string]
	//returns: true if section extists and belongs to this project, false if not [bool]	

	function hasSection($UID) {
		if (false == $this->sectionsLoaded) { $this->loadSections(); }
		foreach($this->sections as $section) {
			if ($UID == $section['UID']) { return true; }
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a section
	//----------------------------------------------------------------------------------------------
	//arg: title - section title [string]
	//arg: weight - weight of this section [int]
	//opt: content - html [string]
	//returns: UID of new section on success, empty string on failure [string]

	function addSection($title, $weight, $content = '') {
		global $session;

		$model = new Projects_Section();
		$model->parent = 'root';
		$model->projectUID = $this->UID;
		$model->title = $title;
		$model->weight = $weight;
		$model->content = $content;
		$report = $model->save();
		
		if ('' == $report) { 
			$this->loadSections();
			return $model->UID; 
		}
		$kapenta->session->msg('Could not create project section:<br/>' . $report, 'bad');
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a section
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of the Projects_Section object to be removed [string]
	//returns: true if the section was deleted, false if not found [bool]

	function deleteSection($UID) {
		$model = new Projects_Section();
		if (false == $model->loaded) { return false; }
		$check = $model->delete();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	find the section with the largest weight
	//----------------------------------------------------------------------------------------------
	//returns: weight of the heaviest section [int]

	function getMaxWeight() {
		if (false == $this->sectionsLoaded) { $this->loadSections(); }
		$maxWeight = 0;														//%	return value [int]

		foreach($this->sections as $section) {
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
		if (false == $this->sectionsLoaded) { $this->loadSections(); }
		if (false == $this->hasSection($sectionUID)) { return false; }

		$oldWeight = $this->sections[$sectionUID]['weight'];		// this section's weight

		if ($this->getMaxWeight() == $oldWeight) { return false; }	// already max weight

		//------------------------------------------------------------------------------------------
		//	decrement section above this one
		//------------------------------------------------------------------------------------------
		foreach($this->sections as $currUID => $section) {
			if (($oldWeight + 1) == $section['weight']) {
				$this->sections[$currUID]['weight'] = $oldWeight;
				$model = new Projects_Section($currUID);
				if (true == $model->loaded) {
					$model->weight = $oldWeight;
					$model->save();
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	increment this section
		//------------------------------------------------------------------------------------------
		$this->sections[$sectionUID]['weight'] = $oldWeight + 1;	// inc self
		$model = new Projects_Section($sectionUID);
		if (true == $model->loaded) {
			$model->weight = $oldWeight + 1;
			$model->save();
		}

		$this->loadSections();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	decrease a section's weight
	//----------------------------------------------------------------------------------------------
	//arg: sectionUID - UID of a section [string]
	//returns: true if decremented, false if not [bool]

	function decrementSection($sectionUID) {
		if (false == $this->sectionsLoaded) { $this->loadSections(); }
		if (false == $this->hasSection($sectionUID)) { return false; }

		$oldWeight = $this->sections[$sectionUID]['weight'];		//% this section's weight [int]
		if (1 == $oldWeight) { return false; }						// already min weight

		//------------------------------------------------------------------------------------------
		// increment section above this one
		//------------------------------------------------------------------------------------------
		foreach($this->sections as $currUID => $section) {			// inc section above
			if (($oldWeight - 1) == $section['weight']) {
				$this->sections[$currUID]['weight'] = $oldWeight;
				$model = new Projects_Section($currUID);
				if (true == $model->loaded) {
						$model->weight = $oldWeight + 1;
						$model->save();
				}
			}
		}

		//------------------------------------------------------------------------------------------
		// decrement this section's weight
		//------------------------------------------------------------------------------------------
		$this->sections[$sectionUID]['weight'] = $oldWeight - 1;
		$model = new Projects_Section($sectionUID);
		if (true == $model->loaded) {
			$model->weight = $oldWeight + 1;
			$model->save();
		}

		$this->loadSections();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user is an admin or member of a project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if the specified user has edit permissions on this project, else false [bool]

	function hasEditAuth($userUID) {
		global $kapenta;
		return $kapenta->user->authHas('projects', 'projects_project', 'edit', $this->UID);
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
	//	MEMBERSHIPS
	//==============================================================================================

	function getMembers() {
		global $session;
		$kapenta->session->msgAdmin('DEPRECATED: Projects_Project::getMembers()', 'warn');
		return $this->memberships->getMembers();
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

}

?>
