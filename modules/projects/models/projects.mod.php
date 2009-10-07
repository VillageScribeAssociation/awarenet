<?

//--------------------------------------------------------------------------------------------------
//	object for managing projects
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/projects/models/membership.mod.php');
require_once($installPath . 'modules/projects/models/projectrevision.mod.php');

//	Projects are a special type of document co-created by several users.  All users who are members
//	of the project have edit permissions on it and can edit text, add images, etc.
//	A projects states may be 'open', 'finished' or 'cancelled'.  The user who started a project
//	may add members and can set the project's status.

//	On completion, a notification is generated for friends and classmates of all members.
//	Members can edit the abstract and content of the project but only the user who started the
//	project may edit the title.  The content of a project is divided into 'sections' which are 
//	edited independantly.  Same as wiki articles.

//	structure of content:
//  <article>
//	  <section>
//		<UID>1239877129</UID>
//	    <title>Microfauna</title>
//	    <weight>3</weight>
//      <content><![CDATA[This is escaped HTML]]></content>
//	  </section>
//  </article>

class Project {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure
	var $sections;		

	var $sectionFields = 'UID|title|weight|content';
	var $allowTags = '<img><b><i><a><table><tr><td><th><ul><ol><li><span><p><br><font><h1><h2><h3><blockquote><pre>';

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Project($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['name'] = 'New project';
		$this->data['createdBy'] = $user->data['UID'];
		$this->data['createdOn'] = mysql_datetime();
		$this->data['status'] = 'open';
		$this->sections = array();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('projects', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) { 
		$this->data = $ary; 
		$this->expandSections();
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		// ensure that the user who started project is a member
		$this->addMember($this->data['createdBy'], 'admin');

		// collapse all sections
		$this->collapseSections();

		// update recordAlias
		$this->data['recordAlias'] = 
				raSetAlias('projects', $this->data['UID'], $this->data['title'], 'projects');

		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (trim($this->data['title']) == '') { $this->data['title'] = 'Untitled Project'; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'projects';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'title' => 'VARCHAR(255)',
			'abstract' => 'TEXT',
			'content' => 'TEXT',
			'status' => 'VARCHAR(255)',		
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'finishedOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		global $user;
		$ary = $this->data;
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['editAbstractUrl'] = '';
		$ary['editAbstractLink'] = '';

		//------------------------------------------------------------------------------------------
		//	permissions
		//------------------------------------------------------------------------------------------

		$editAuth = false;
		if ( (authHas('projects', 'edit', $this->data) == true) 
			AND ($this->isMember($user->data['UID']) == true) ) { $editAuth = true; }

		$delAuth = false;
		if ($user->data['ofGroup'] == 'admin') { $delAuth = true; }
		// TODO: decide whether project admins can delete a project

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('projects', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%projects/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($editAuth == true) {
			$ary['editUrl'] =  '%%serverPath%%projects/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 

			$ary['editAbstractUrl'] = '%%serverPath%%projects/editabstract/'
									. $this->data['recordAlias'];

			$ary['editAbstractLink'] = "<a href='" . $ary['editAbstractUrl'] . "'>"
									. "[edit abstract]</a>"; 
		}

		if ($delAuth == true) {
			$ary['delUrl'] =  '%%serverPath%%projects/confirmdelete/UID_'. $this->data['UID'] .'/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('projects', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%projects/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new project]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	abstract 
		//------------------------------------------------------------------------------------------

		$ary['abstractHtml'] = str_replace(">\n", ">", $ary['abstract']);
		$ary['abstractHtml'] = str_replace("\n", "<br/>\n", $ary['abstractHtml']);
	
		$ary['summary'] = substr(strip_tags(strip_blocks($ary['abstract'])), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	byline
		//------------------------------------------------------------------------------------------

		$ary['byline'] = array();
		$members = $this->getMembers();
		foreach($members as $userUID => $role) {
			$ary['byline'][] = "[[:users::namelink::userUID=" . $userUID . ":]]";			
		}
		$ary['byline'] = implode(", \n", $ary['byline']);

		//------------------------------------------------------------------------------------------
		//	index
		//------------------------------------------------------------------------------------------

		$ary['indexHtml'] = ''; 
		foreach($this->sections as $UID => $section) {
			$link = "%%serverPath%%projects/" . $ary['recordAlias'] . "#s" . $section['UID'];
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
							. 'section_' . $section['UID'] . '/' . $ary['recordAlias'];
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
	//	make extended array of section data
	//----------------------------------------------------------------------------------------------

	function sectionArray($sectionUID) {
		if (array_key_exists($sectionUID, $this->sections) == false) { return false; }
		$ary = $this->sections[$sectionUID];
		$ary['projectUID'] = $this->data['UID'];
		$ary['projectTitle'] = $this->data['title'];
		$ary['sectionTitle'] = $ary['title'];	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Projects Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create projects table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('projects') == false) {	
			echo "installing projects module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created projects table and indices...<br/>';
		} else {
			$this->report .= 'projects table already exists...<br/>';	
		}

		//------------------------------------------------------------------------------------------
		//	create projectmembers table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('projectmembers') == false) {	
			echo "creating projectmembers table\n";
			$pM = new ProjectMembership();
			dbCreateTable($pM->dbSchema);	
			$this->report .= 'created projectmembers table and indices...<br/>';
		} else {
			$this->report .= 'projectmembers table already exists...<br/>';	
		}

		echo $report;
		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		$sql = "delete from images where refModule='projects' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		$sql = "delete from files where refModule='projects' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		
		raDeleteAll('projects', $this->data['UID']);
		dbDelete('projects', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	expand sections
	//----------------------------------------------------------------------------------------------

	function expandSections() {
		$this->sections = array();
		$sF = explode('|', $this->sectionFields);
		$xe = new XmlEntity($this->data['content']);

		foreach($xe->children as $index => $section) {
			$newSection = array();
			foreach($sF as $field) { 
				$newSection[$field] = '';	
				$value = $section->getFirst($field);
				if ($value != false) { $newSection[$field] = $value; }

			}

			if (strlen($newSection['UID'] > 2)) {
				$this->sections[$newSection['UID']] = $newSection;
			}

		}		
	}

	//----------------------------------------------------------------------------------------------
	//	collapse sections
	//----------------------------------------------------------------------------------------------

	function collapseSections() {
		global $installPath; // remove
		$sF = explode('|', $this->sectionFields);
		$xe = new XmlEntity();
		$xe->type = 'article';

		foreach($this->sections as $section) {
			$sxe = new XmlEntity();
			$sxe->type = 'section';
			$sxe->isRoot = false;
			foreach($sF as $field) {
				$childCDATA = FALSE;
				if ($field == 'content') { 
					if ('' == $section[$field]) { $section[$field] = ' '; }
					$childCDATA = TRUE; 
				}
				$sxe->addChild($field, $section[$field], $childCDATA);
			}
			$xe->children[] = $sxe;
		}

		$this->data['content'] = $xe->toString();	

		//$tmpFile = $installPath . 'modules/projects/tmp.xml';
		//$fh = fopen($tmpFile, 'w+');
		//fwrite($fh, $this->data['content']);
		//fclose($fh);

	}

	//----------------------------------------------------------------------------------------------
	//	sort sections by weight
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
	//	add a section
	//----------------------------------------------------------------------------------------------

	function addSection($title, $weight) {
		$newSection = array();
		$newSection['UID'] = createUID();
		$newSection['title'] = $title;
		$newSection['weight'] = $weight;
		$newSection['content'] = '';
		$this->sections[$newSection['UID']] = $newSection;
		$this->sort();
		$this->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a section
	//----------------------------------------------------------------------------------------------

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
	//	find the section with the largest weight
	//----------------------------------------------------------------------------------------------

	function getMaxWeight() {
		$maxWeight = 0;
		foreach($this->sections as $sUID => $section) {
			if ($section['weight'] > $maxWeight) { $maxWeight = $section['weight']; }		
		}
		return $maxWeight;
	}

	//----------------------------------------------------------------------------------------------
	//	increase a section's weight
	//----------------------------------------------------------------------------------------------

	function incrementSection($sectionUID) {
		if (array_key_exists($sectionUID, $this->sections) == false) { return false; }
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
	//	decrease a section's weight
	//----------------------------------------------------------------------------------------------

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
	//	add a member to the project, or change the role of an existing member
	//----------------------------------------------------------------------------------------------

	function addMember($userUID, $role) {
		if ($this->isMember($userUID) == true) {
			$pM = new ProjectMembership($this->data['UID'], $userUID);
			$pM->data['role'] = $role; 
			$pM->save();
		} else {
			$pM = new ProjectMembership();
			$pM->data['UID'] = createUID();
			$pM->data['projectUID'] = $this->data['UID'];
			$pM->data['userUID'] = $userUID;
			$pM->data['role'] = $role;
			$pM->save();
		}
	}
	//----------------------------------------------------------------------------------------------
	//	remove a member from the project
	//----------------------------------------------------------------------------------------------

	function removeMember($userUID) {
		if ($this->isMember($userUID) == true) {
			$pM = new ProjectMembership($this->data['UID'], $userUID);
			$pM->delete();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	determine if a given user (UID) is a member of the project
	//----------------------------------------------------------------------------------------------

	function isMember($userUID) {
		$pM = new ProjectMembership();
		return $pM->load($this->data['UID'], $userUID);
	}

	//----------------------------------------------------------------------------------------------
	//	get all members of a project, returns array of [userUID] => [role]
	//----------------------------------------------------------------------------------------------

	function getMembers() {
		$retVal = array();
		$sql = "select * from projectmembers "
			 . "where projectUID='" . $this->data['UID'] . "' and role != 'asked'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$retVal[$row['userUID']] = $row['role'];
		}
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	get prospective members of a project, returns array of [userUID] => [role]
	//----------------------------------------------------------------------------------------------

	function getProspectiveMembers() {
		$retVal = array();
		$sql = "select * from projectmembers "
			 . "where projectUID='" . $this->data['UID'] . "' and role='asked'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$retVal[$row['userUID']] = $row['role'];
		}
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	determine if a given user is an admin or member of a project
	//----------------------------------------------------------------------------------------------

	function hasEditAuth($userUID) {
		$members = $this->getMembers();
		foreach($members as $mUID => $role) {
			if (($userUID == $mUID) && (($role == 'admin')||($role == 'member'))) { return true; } 
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	convert to html for diff
	//----------------------------------------------------------------------------------------------

	function getSimpleHtml() {
		$html = "<h1>" . $this->data['title'] . "</h1>" . $this->data['abstract'];
		foreach($this->sections as $key => $section) {
			$html .= "<h2>" . trim($section['title']) . "</h2>" . trim($section['content'])
				  . "<p><small>weight: " . $section['weight'] . " uid: " . $section['UID']
				  . " title: " . $section['title'] . "</small></p>";
		}
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	save a revision
	//----------------------------------------------------------------------------------------------

	function saveRevision() {
		$revision = new ProjectRevision();
		$revision->data['refUID'] = $this->data['UID'];
		$revision->data['type'] = 'newsection';
		$revision->data['content'] = $this->getSimpleHtml();
		$revision->save();
	}

	//----------------------------------------------------------------------------------------------
	//	shorthand
	//----------------------------------------------------------------------------------------------

	function getUrl() { return "%%serverPath%%projects/" . $this->data['recordAlias']; }
	function getLink() { return "<a href='". $this->getUrl() ."'>". $this->data['title'] ."</a>";}
}

?>
