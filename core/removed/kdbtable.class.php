<?

//--------------------------------------------------------------------------------------------------
//*	a simple DBMS to serve as an object store when MySQL is not available
//--------------------------------------------------------------------------------------------------
//+	records are stored in as escaped XML, indices record their position and size in the text file
//+
//+	when updating a record it is overwritten if the new copy is smaller than the old, or placed in 
//+ free space if larger, and the original overwritten with whitespace
//+
//+	a whitespace index is also maintained (?)

class KDBTable {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $name;				// table name [string]
	var $fields;			// database fields [array]
	var $indices;			// fields indexes [array]
	var $metaFile;			// abs path to file containing table metadata [string]
	var $dataFile;			// abs path to file containing records [string]
	var $fH = false;		// data file handle [int]
	var $loaded = false;	// set to true when table meta is loaded

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: name	- name of an extant table [string]

	function KDBTable($name = '') {
		global $kapenta;
		if ('' != $name) { $this->load($name); }		// try load a table if name is given
		if (false == $this->loaded) {					// set up some default values if not loaded
			$this->fields = array();
			$this->indices = array();
			$this->name = $kapenta->createUID();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load table metadata
	//----------------------------------------------------------------------------------------------
	//opt: name	- name of an extant table [string]
	//returns: true on success, false on failure [bool]	

	function load($name) {
		global $kapenta;

		$this->fields = array();
		$this->indices = array();

		// TODO: security check on name (must be alphanumeric, no dots, etc)
		$this->metaFile = $kapenta->installPath . 'data/db/' . $name . '.meta.php';
		$this->dataFile = $kapenta->installPath . 'data/db/' . $name . '.table.php';
		if (false == file_exists($this->metaFile)) { return false; }
	
		$raw = $kapenta->fileGetContents($this->metaFile));	// TODO: better file wrappers
		$xe = new XmlEntity($raw);	//TODO: use KXmlDocument

		echo $this->metaFile . "<br/><textarea rows='10' cols='80'>$raw</textarea><br/>\n";

		if ('table' == $xe->type) {
			foreach($xe->children as $child) {
				// get name
				if ('name' == $child->type) { $this->name = $child->value; }

				// add fields
				if ('fields' == $child->type) {
					foreach($child->children as $field) {
						$name = '';
						$type = '';
						foreach($field->children as $part) {
							if ('name' == $part->type) { $name = $part->value; }
							if ('type' == $part->type) { $type = $part->value; }
						}
						if (('' != $name) && ('' != $type)) { $this->fields[$name] = $type; }
					}
				}

				// add indices
				if ('indices' == $child->type) {
					foreach($child->children as $field) {
						$on = '';
						$size = '';
						foreach($field->children as $blah) {
							if ('on' == $blah->type) { $on = $blah->value; }
							if ('size' == $blah->type) { $size = $blah->value; }
						}
						if (('' != $on) && ('' != $size)) { $this->fields[$on] = $size; }
					}
				}
				
			}
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save table metadata
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]	

	function save() {
		global $installPath;
		// TODO: admin check here
		//if (false == $this->loaded) { return false; }		
		if ('' == $this->name) { return false; }
		$this->metaFile = $installPath . 'data/db/' . $this->name . '.meta.php';

		$raw = "<? /*\n"
			 . "<table>\n"
			 . "\t<name>" . $this->name . "</name>\n"
			 . "\t<fields>\n";
		
		foreach($this->fields as $name => $type) 
			{ $raw .= "\t\t<field><name>$name</name><type>$type</type></field>\n"; }

		$raw .= "\t</fields>\n\t<indices>\n";

		foreach($this->indices as $field => $size) 
			{ $raw .= "\t\t<index><on>$field</on><size>$size</size></index>\n"; }

		$raw .= "\t</indices>\n"
			 . "</table>\n"
			 . "/* ?>";

		filePutContents($this->metaFile, $raw, 'w+');
	}

	//----------------------------------------------------------------------------------------------
	//.	return table schema as nested assciative arrays
	//----------------------------------------------------------------------------------------------
	//returns: table schema (dbSchema format) [array]	

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['name'] = $this->name;
		$dbSchema['fields'] = $this->fields;
		$dbSchema['indices'] = $this->indices;
		return $dbSchema;
	}

}

?>
