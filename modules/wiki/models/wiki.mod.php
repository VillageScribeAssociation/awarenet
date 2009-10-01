<?

//--------------------------------------------------------------------------------------------------
//	object for managing wiki articles
//--------------------------------------------------------------------------------------------------

//	Note: articles are divided up into sections based on wiki markup.  This is so that sections
//	may be modified independantly of each other to minimise collisions when multiple users are
//	editing the same document.
//
//	Note: this module has many satellite tables, with their own models for building and
//	maintaining the wiki.  They are:
//
//	wikicategories.mod.php - heirarchies of categories to which articles may belong
//	wikicatindex.mod.php - associates articles with categories
//	wikirevisions.mod.php - article histories (diff, revert changes, etc)
//	wikidelete.mod.php - tracks articles nominated for deletion
//
//	Permissions may be set on each article as to who may edit it, these override module-wide
//	permissions for a single article only, and only to the extent Kapenta settings allow.  The
//	lock field may be set to a user group (public/user/admin).

require_once($installPath . 'modules/wiki/models/wikicode.mod.php');

class Wiki {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	var $defaultIndexPage = 'modules/wiki/index.wiki.php';	// php enclosed wikicode
	var $content;
	var $talk;
	var $expanded = false;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Wiki($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['title'] = 'New Article ' . $this->data['UID'];
		$this->data['createdBy'] = $user->date['UID'];
		$this->data['createdOn'] = mysql_datetime();
		$this->data['editedBy'] = $user->date['UID'];
		$this->data['editedOn'] = mysql_datetime();
		$this->data['locked'] = 'user';
		$this->data['hitcount'] = '0';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('wiki', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('wiki', $d['UID'], $d['title'], 'wiki');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'wiki';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'talk' => 'TEXT',
			'locked' => 'VARCHAR(20)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'hitcount' => 'BIGINT',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');

		$dbSchema['nodiff'] = array( 'UID', 'title', 'content', 'talk', 'locked', 'createdBy', 
									 'createdOn', 'editedBy', 'editedOn', 'hitcount', 
									 'recordAlias' );
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;
		$ary['editUrl'] = '';	$ary['editLink'] = '';	// edit
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view
		$ary['newUrl'] = '';	$ary['newLink'] = '';	// new article form
		$ary['nomUrl'] = '';	$ary['nomLink'] = '';	// nominate for deletion
		$ary['delUrl'] = '';	$ary['delLink'] = '';	// delete

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('wiki', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='%%serverPath%%wiki/" . $ary['recordAlias'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}

		if (authHas('wiki', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%wiki/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['nomUrl'] =  '%%serverPath%%wiki/nominatedelete/' . $this->data['recordAlias'];
			$ary['nomLink'] = "<a href='" . $ary['nomUrl'] . "'>[nominate for deletion]</a>"; 
		}

		if (authHas('wiki', 'delete', $this->data)) {
			$ary['delUrl'] = '%%serverPath%%wiki/confirmdelete/uid_' . $this->data['UID'];
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>";
		}

		if (authHas('wiki', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%wiki/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new article]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	sanitize content for editing
		//------------------------------------------------------------------------------------------

		$ary['content'] = str_replace('[[:', '[[%%delme%%:', $ary['content']);

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['createdOnLong'] = date('jS F, Y', strtotime($ary['createdOn']));
		$ary['editedOnLong'] = date('jS F, Y', strtotime($ary['editedOn']));
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
			$ary['contents'] = $this->content->contents;
			$ary['contentHtml'] = expandBlocks($this->content->html, '');
			$ary['infobox'] = $this->content->infobox;
			$ary['talkContents'] = $this->talk->contents;
			$ary['talkHtml'] = $this->talk->html;
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
	//	expand wiki text and talk pages
	//----------------------------------------------------------------------------------------------

	function expandWikiCode() {
		$this->content = new WikiCode();
		$this->content->source = $this->data['content'];
		$this->content->expandWikiCode();

		$this->talk = new WikiCode();
		$this->talk->source = $this->data['talk'];
		$this->talk->expandWikiCode();

		$this->expanded = true;
	}

	//----------------------------------------------------------------------------------------------
	//	delete an article and all its assets
	//----------------------------------------------------------------------------------------------

	function delete() {
		if (dbRecordExists('wiki', $this->data['UID']) == false) { return false; }

		// delete all category memberships
		// TODO

		// delete all revisions
		// TODO

		// delete all images owned by this article
		// TODO

		// delete all files owned by this article
		// TODO		

		// remove nomination for deletion (if exists)
		// TODO

		// delete all recordAliases
		raDeleteAll('wiki', $this->data['UID']);

		// delete the record
		dbDelete('wiki', $this->data['UID']);

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	create the default page (index)
	//----------------------------------------------------------------------------------------------

	function mkDefault() {
		global $user;
		global $installPath;

		$this->data['title'] = 'Index';
		$this->data['createdBy'] = $user->data['UID'];
		$this->data['createdOn'] = mysql_datetime();
		$this->data['editedBy'] = $user->data['UID'];
		$this->data['editedOn'] = $user->data[''];

		$fileName = $installPath . $this->defaultIndexPage;
		$raw = implode(file($fileName));
		$this->data['content'] = phpUnComment($raw);

		$this->data['talk'] = '';
		$this->data['locked'] = 'admin';
		$this->data['hitcount'] = '0';
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Wiki Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create wiki table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('wiki') == false) {	
			echo "installing wiki module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created wiki table and indices...<br/>';
		} else {
			$this->report .= 'wiki table already exists...<br/>';	
		}

		return $report;
	}

}

?>
