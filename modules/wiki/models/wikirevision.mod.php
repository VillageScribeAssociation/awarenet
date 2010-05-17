<?

//--------------------------------------------------------------------------------------------------
//*	object for manging wiki revisions
//--------------------------------------------------------------------------------------------------

class WikiRevision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database table structure [array]


	var $allRevisions;	// php enclosed wikicode [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a wiki revision [string]

	function WikiRevision($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['refUID'] = '';
		$this->data['content'] = '';
		$this->data['editedBy'] = $user->date['UID'];
		$this->data['editedOn'] = mysql_datetime(time());
		$this->allRevisions = array();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a wiki revision [string]

	function load($UID) {
		$ary = dbLoad('wikirevisions', $UID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		$d = $this->data;
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that an object is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'wikirevisions';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'refUID' => 'VARCHAR(30)',		
			'content' => 'TEXT',
			'type' => 'VARCHAR(50)',
			'reason' => 'VARCHAR(255)',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10');

		$dbSchema['nodiff'] = array( 'UID', 'refUID', 'content', 'type', 'reason', 
									 'editedBy', 'editedOn', 'recordAlias' );
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		$ary = $this->data;
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (authHas('wiki', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='%%serverPath%%wiki/" . $ary['recordAlias'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}

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
	//.	delete the current revision from the database
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function delete() {
		if (dbRecordExists('wikirevisions', $this->data['UID']) == false) { return false; }
		dbDelete('wikirevisions', $this->data['UID']);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	find UID of previous version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------
	//returns: UID of previous revision, false if not found [string][bool]

	function getPreviousUID() {
		foreach($this->allRevisions as $key => $row) {	// for each revision
			if ($row['UID'] == $this->data['UID']) {	// if this one is found

				if ($key > 0) {
					$prev = $this->allRevisions[($key - 1)];
					return $prev['UID'];
				} else {
					return false;	// this is the first revision
				}

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	find UID of next version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------
	//returns: UID of next revision, false if not found [string][bool]

	function getNextUID() {
		foreach($this->allRevisions as $key => $row) {	// for each revision
			if ($row['UID'] == $this->data['UID']) {	// if this one is found

				if ($key != (count($this->allRevisions) - 1)) {
					$next = $this->allRevisions[($key + 1)];
					return $next['UID'];
				} else {
					return false;	// this is the latest revision
				}

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	find all revisions to this wiki article or talk page, make list at $this->allRevisions
	//----------------------------------------------------------------------------------------------

	function getAllRevisions() {
		if ($this->data['refUID'] == '') { return false; }
		$this->allRevisions = array();
		//TODO: use dbLoadRange
		$sql = "select UID, refUID, type, reason, editedBy, editedOn from wikirevisions "
			 . "where refUID='" . $this->data['refUID'] . "' "
			 . "and type='" . $this->data['type'] . "' "
			 . "order by editedOn";	// least to most recent

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $this->allRevisions[] = sqlRMArray($row); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.php

	function install() {
		$report = "<h3>Installing Wiki Revisions</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create wiki table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('wikirevisions') == false) {	
			echo "crating table: wikirevisions\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created wikirevisions table and indices...<br/>';
		} else {
			$this->report .= 'wikirevisions table already exists...<br/>';	
		}

		return $report;
	}

}

?>
