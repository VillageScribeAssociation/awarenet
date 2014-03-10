<?

	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');

//--------------------------------------------------------------------------------------------------
//*	object representing wiki talk pages
//--------------------------------------------------------------------------------------------------
//+	These are very similar to wiki articles

class Wiki_Talk {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $content;			//_ text [string]
	var $nav;				//_ text [string]
	var $locked;			//_ varchar(30) [string]
	var $namespace;			//_ varchar(30) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	var $wikicode;

	var $defaultIndexPage = 'modules/wiki/index.wiki.php';	// php enclosed wikicode [string]
	var $talk;												//
	var $expanded = false;									// [bool]


	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Article object [string]

	function Wiki_Talk($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		$this->wikicode = new WikiCode();
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did	
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Article ' . $this->UID;		// set default title
			$this->locked = 'user';					// TODO - make this a setting
		}
	}


	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Article object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if (false != $objary) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Article object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->nav = $ary['nav'];
		$this->locked = $ary['locked'];
		$this->namespace = $ary['namespace'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;

		$this->wikicode->source = $this->content;

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
		$this->alias = $aliases->create('wiki', 'Wiki_Talk', $this->UID, $this->title);
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
		$dbSchema['module'] = 'wiki';
		$dbSchema['model'] = 'Wiki_Talk';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'articleUID' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'nav' => 'TEXT',
			'locked' => 'VARCHAR(30)',
			'namespace' => 'VARCHAR(33)',
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

		//revision history kept by wiki module, not default revision system
		$dbSchema['nodiff'] = array(
			'UID',
			'title',
			'content',
			'nav',
			'locked',
			'namespace',
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
			'content' => $this->content,
			'nav' => $this->nav,
			'locked' => $this->locked,
			'namespace' => $this->namespace,
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
		global $user;
		global $theme;

		$ary = $this->toArray();				//%	return value [dict]

		$ary['editUrl'] = '';	$ary['editLink'] = '';	// edit
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view
		$ary['newUrl'] = '';	$ary['newLink'] = '';	// new article form
		$ary['nomUrl'] = '';	$ary['nomLink'] = '';	// nominate for deletion
		$ary['delUrl'] = '';	$ary['delLink'] = '';	// delete

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if ($user->authHas('wiki', 'Wiki_Talk', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/' . $ary['alias'];
			$ary['viewLink'] = "<a href='%%serverPath%%wiki/" . $ary['alias'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}

		if ($user->authHas('wiki', 'Wiki_Talk', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%wiki/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['nomUrl'] =  '%%serverPath%%wiki/nominatedelete/' . $this->alias;
			$ary['nomLink'] = "<a href='" . $ary['nomUrl'] . "'>[nominate for deletion]</a>"; 
		}

		if ($user->authHas('wiki', 'Wiki_Talk', 'delete', $this->UID)) {
			$ary['delUrl'] = '%%serverPath%%wiki/confirmdelete/uid_' . $this->UID;
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>";
		}

		if ($user->authHas('wiki', 'Wiki_Talk', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%wiki/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new article]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	sanitize content for editing
		//------------------------------------------------------------------------------------------

		$ary['contentSafe'] = str_replace('[[:', '[[%%delme%%:', $ary['content']);
		$ary['navSafe'] = str_replace('[[:', '[[%%delme%%:', $ary['nav']);

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['createdOnLong'] = $kapenta->longDate($ary['createdOn']);
		$ary['editedOnLong'] = $kapenta->longDate($ary['editedOn']);
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	if the wikicode has been expanded, compile contents block, document, languages, etc
		//------------------------------------------------------------------------------------------

		$ary['contents'] = '';
		$ary['contentHtml'] = '';
		$ary['talkContents'] = '';
		$ary['talkHtml'] = '';
		$ary['infobox'] = '';
		$ary['interlingua'] = '';	// TODO
		$ary['seealso'] = '';		// TODO
		$ary['references'] = '';	// TODO


		if (count($this->expanded) == true) {
			$ary['contents'] = $this->wikicode->contents;
			$ary['contentHtml'] = $theme->expandBlocks($this->wikicode->html, '');
			$ary['infobox'] = $this->wikicode->infobox;
			//$ary['talkContents'] = $this->talk->contents;	// TODO - stabilize
			//$ary['talkHtml'] = $this->talk->html;
		}

		//------------------------------------------------------------------------------------------
		//	avoid collision with page title
		//------------------------------------------------------------------------------------------

		$ary['articleTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		//$ary['summary'] = strip_tags($ary['content']);
		//$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';
		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand wiki text and talk pages
	//----------------------------------------------------------------------------------------------

	function expandWikiCode() {
		$this->wikicode = new WikiCode(); 
		//$this->content->source = $this->content;
		$this->wikicode->source = $this->content;
		$this->wikicode->navsource = $this->nav;
		$this->wikicode->expandWikiCode();

		//$this->talk = new WikiCode();
		//$this->talk->source = $this->talk;
		//$this->talk->expandWikiCode();

		$this->expanded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set this article to be the default page
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function mkDefault() {
		global $kapenta;
		global $user;


		$this->title = 'Index';
		$raw = $kapenta->fs->get($this->defaultIndexPage, false, true);
		if (false == $raw) { $raw = "(editable by admins only)"; }
		$this->content = $raw;
		$this->locked = 'admin';
		return true;
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
