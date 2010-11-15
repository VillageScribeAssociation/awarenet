<?

//--------------------------------------------------------------------------------------------------
//	object for managing directory posts
//--------------------------------------------------------------------------------------------------

class Contact {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;		// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Contact($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('contact', $uid);
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
		$dbSchema['table'] = 'contact';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'refUID' => 'VARCHAR(30)',
			'refModule' => 'VARCHAR(255)',
			'type' => 'VARCHAR(50)',
			'description' => 'VARCHAR(255)',
			'value' => 'TEXT',
			'isDefault' => 'VARCHAR(4)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'refUID' => '10', 
			'refModule' => '10', 
			'type' => '10');

		$dbSchema['nodiff'] = array('UID', 'refModule', 'refUID');

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
		$ary = $this->data;

		//------------------------------------------------------------------------------------------
		//	links and URLs
		//------------------------------------------------------------------------------------------

		$ary['ifUrl'] = '/contact/refModule_'. $ary['refModule'] .'/refUID_'. $ary['refUID'] .'/';
		$ary['mkDefaultUrl'] = '';
		$ary['mkDefaultLink'] = '';
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		if (authHas($ary['refModule'], 'editcontact', '') == true) {
			$ary['editUrl'] = '/contact/edit/' . $ary['UID'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>";
			$ary['newUrl'] = $ary['ifUrl'] . 'new/';
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new]</a>";
			$ary['delUrl'] = '/contact/delete/' . $ary['UID'];
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>";
			
			if ($ary['isDefault'] != 'true') {
				$ary['mkDefaultUrl'] = '/contact/mkdefault/' . $ary['UID'];
				$ary['mkDefaultLink'] = "<a href='" . $mkDefaultUrl . "'>[mk default]</a>";
			} else {
				$ary['mkDefaultLink'] = "(default)";
			}
		}

		//------------------------------------------------------------------------------------------
		//	special types
		//------------------------------------------------------------------------------------------

		$ary['extValue'] = str_replace("\n", "<br/>\n", $ary['value']);
		switch($ary['type']) {
			case 'web page':	
					$ary['extValue'] = "<a href='". $ary['value'] ."' target='". createUID() ."'>"
										 . $ary['value'] . "</a>";
					break;

			case 'email address':
					$ary['extValue'] = "<a href='mailto:" . $ary['value'] . "'>" 
									 . $ary['value'] . "</a>";
					break;
		}

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Contact Details Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create directory table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('contact') == false) {	
			dbCreateTable($this->dbSchema);	
			$report .= 'created contact table and indices...<br/>';
		} else {
			$report .= 'contact table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete the current entry
	//----------------------------------------------------------------------------------------------
	
	function delete() {		
		dbDelete('contact', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	make a record the default for its type
	//----------------------------------------------------------------------------------------------

	function mkDefault($UID, $refUID, $refModule, $type) {
		$sql = "UPDATE contact SET isDefault='false'" 
			 . " WHERE refUID='" . sqlMarkup($refUID) . "'"
			 . " AND refModule='" . sqlMarkup($refModule) . "'"
			 . " AND type='" . sqlMarkup($type) . "'";

		dbQuery($sql);

		$sql = "UPDATE contact SET isDefault='true' WHERE UID='" . sqlMarkup($UID) . "'";
		dbQuery($sql);
	}

	//----------------------------------------------------------------------------------------------
	//	set default for all contact detail types for which there is only one entry
	//----------------------------------------------------------------------------------------------

	function autoDefault($refUID) {
		$sql = "select type, count(UID) as num from contact"
			 . " where refUID='" . sqlMarkup($refUID) . "' group by type";

		$results = dbQuery($sql);
		while ($row = dbFetchAssoc($results)) {
			if ($row['num'] == 1) {
				$sql = "UPDATE contact SET isDefault='true'"
					 . " WHERE refUID='" . sqlMarkup($refUID) . "'"
					 . " AND type='" . $row['type'] . "'";

				dbQuery($sql);
			}
		}
	}
	
}

?>
