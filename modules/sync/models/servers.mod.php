<?

//--------------------------------------------------------------------------------------------------
//	object for managing server trust relationships
//--------------------------------------------------------------------------------------------------

class Server {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Server($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('servers', $uid, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		return true;
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
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'servers';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'servername' => 'VARCHAR(50)',
			'serverurl' => 'VARCHAR(100)',			
			'password' => 'VARCHAR(50)',
			'direction' => 'VARCHAR(50)',
			'active' => 'VARCHAR(10)'
			);

		$dbSchema['indices'] = array('UID' => '10');

		$dbSchema['nodiff'] = array('UID', 'password');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;	
		
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('sync', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%sync/server/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('sync', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%sync/editserver/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('sync', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%sync/delserver/' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Sync (server) Model</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (dbTableExists('servers') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created servers table and indices...<br/>';
		} else {
			$this->report .= 'servers table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a server record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('servers', $this->data['UID']);
	}
	
}
?>
