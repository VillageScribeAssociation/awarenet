<?

//--------------------------------------------------------------------------------------------------
//*	object representing static pages
//--------------------------------------------------------------------------------------------------

class Home_Static {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $template;			//_ varchar(30) [string]
	var $content;			//_ text [string]
	var $nav1;				//_ text [string]
	var $nav2;				//_ plaintext [string]
	var $script;			//_ plaintext [string]
	var $jsinit;			//_ plaintext [string]
	var $banner;			//_ varchar(255) [string]
	var $menu1;				//_ plaintext [string]
	var $menu2;				//_ plaintext [string]
	var $breadcrumb;		//_ plaintext [string]
	var $section;			//_ varchar(255) [string]
	var $subsection;		//_ varchar(255) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users-user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users-user [string]
	var $alias;				//_ alias [string]


	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - alias or UID of a static page [string]

	function Home_Static($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Static Page ' . $this->UID;
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Static object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Static object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->template = $ary['template'];
		$this->content = $ary['content'];
		$this->nav1 = $ary['nav1'];
		$this->nav2 = $ary['nav2'];
		$this->script = $ary['script'];
		$this->jsinit = $ary['jsinit'];
		$this->banner = $ary['banner'];
		$this->menu1 = $ary['menu1'];
		$this->menu2 = $ary['menu2'];
		$this->breadcrumb = $ary['breadcrumb'];
		$this->section = $ary['section'];
		$this->subsection = $ary['subsection'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('home', 'Home_Static', $this->UID, $this->title);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'home';
		$dbSchema['model'] = 'Home_Static';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'template' => 'VARCHAR(30)',
			'content' => 'TEXT',
			'nav1' => 'TEXT',
			'nav2' => 'TEXT',
			'script' => 'TEXT',
			'jsinit' => 'TEXT',
			'banner' => 'VARCHAR(255)',
			'menu1' => 'TEXT',
			'menu2' => 'TEXT',
			'breadcrumb' => 'TEXT',
			'section' => 'VARCHAR(255)',
			'subsection' => 'VARCHAR(255)',
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
		$dbSchema['nodiff'] = array();

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
			'template' => $this->template,
			'content' => $this->content,
			'nav1' => $this->nav1,
			'nav2' => $this->nav2,
			'script' => $this->script,
			'jsinit' => $this->jsinit,
			'banner' => $this->banner,
			'menu1' => $this->menu1,
			'menu2' => $this->menu2,
			'breadcrumb' => $this->breadcrumb,
			'section' => $this->section,
			'subsection' => $this->subsection,
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
		global $user;
		$ary = $this->toArray();
		$ary['editLink'] = '';
		$ary['viewLink'] = '';
		$ary['newLink'] = '';
		$ary['delLink'] = '';
		
		$ary['editUrl'] = '';
		$ary['viewUrl'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if ($user->authHas('home', 'Home_Static', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%home/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[permalink]</a>"; 
		}

		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%home/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) { 
				$ary['newUrl'] = "%%serverPath%%home/new/"; 
				$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[new]</a>";
		}
		
		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%home/confirmdelete/' . $this->alias;
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
	
		$ary['contentJs'] = $ary['content'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);

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

}

?>
