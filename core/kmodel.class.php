<?

//--------------------------------------------------------------------------------------------------
//*	object representing kapenta models
//--------------------------------------------------------------------------------------------------
//+	As read from module.xml.php files.  This class decribes permissions and relationships the module
//+	exposes for this object type.

class KModel {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	var $loaded = false;		//_	set to true when object has been loaded [bool]
	var $name = '';				//_	name of a model [string]
	var $description = '';		//_	description of a model [string]
	var $permissions;			//_	permissions native to this object [array:string]
	var $export;				//_	permissions exported by this object [array:string]
	var $relationships;			//_	relationships between this object and users [array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: xml - xml snippet describing a model [string]

	function KModel($xml = '') {
		$this->loadXml($xml); 
	}

	//----------------------------------------------------------------------------------------------
	//.	load XML
	//----------------------------------------------------------------------------------------------
	//arg: xml - section of module.xml.php [string]
	//returns: true on success, false on failure [bool]

	function loadXml($xml) {
		//------------------------------------------------------------------------------------------
		//	(re)initialize all members
		//------------------------------------------------------------------------------------------
		$this->name = '';
		$this->description = '';
		$this->permissions = array();
		$this->export = array();
		$this->relationships = array();
		if ('' == trim($xml)) { return false; }

		$doc = new KXMLDocument($xml);
		//echo "creating xml document<br/>\n";
		$root = $doc->getEntity(1);							// try get root entity
		if (false == $root) { return false; }				// check that we did		
		if ('model' != $root['type']) { return false; }
		foreach($root['children'] as $childId) {
			$child = $doc->getEntity($childId);
			switch($child['type']) {
				case 'name':			$this->name = $child['value'];				break;
				case 'description':		$this->description = $child['value'];		break;
				case 'permissions':
					foreach($child['children'] as $permissionId) {
						$permission = $doc->getEntity($permissionId);
						if ('permission' == $permission['type']) 
							{ $this->permissions[] = $permission['value']; }
						if ('export' == $permission['type']) 
							{ $this->export[] = $permission['value']; }
					}
					break;

				case 'relationships':
					foreach($child['children'] as $relationshipId) {
						$relationship = $doc->getEntity($relationshipId);
						if ('relationship' == $relationship['type']) 
							{ $this->relationships[] = $relationship['value']; }
					}
					break;				

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load object serialized as an array
	//----------------------------------------------------------------------------------------------
	//arg: ary - kmodel object serialized as array [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		//TODO: checks here, make sure all parts are present and correct
		$this->name = $ary['name'];
		$this->description = $ary['description'];
		$this->permissions = $ary['permissions'];
		$this->export = $ary['export'];
		$this->relationships = $ary['relationships'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to XML
	//----------------------------------------------------------------------------------------------
	//opt: i - indent, prefix each line with this [string]
	//opt: uI - unit of indentation [string]
	//returns: XML [string]	

	function toXml($i = '', $uI = '    ') {
		$xml = '';	//%	return value [string]
		$xml .= $i . "<model>\n";
		$xml .= $i . $uI . "<name>" . $this->name . "</name>\n";
		$xml .= $i . $uI . "<description>" . $this->description . "</description>\n";
		$xml .= $i . $uI . "<permissions>\n";

		foreach($this->permissions as $permission) 
			{ $xml .= $i . $uI . $uI . "<permission>" . $permission . "</permission>\n"; }

		foreach($this->export as $export) 
			{ $xml .= $i . $uI . $uI . "<export>" . $export . "</export>\n"; }

		$xml .= $i . $uI . "</permissions>\n";
		$xml .= $i . $uI . "<relationships>\n";

		foreach($this->relationships as $relationship) 
			{ $xml .= $i . $uI . $uI . "<relationship>" . $relationship . "</relationship>\n"; }

		$xml .= $i . $uI . "</relationships>\n";
		$xml .= $i . "</model>\n";
		return $xml;
	}


	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: this object serialized as an array [array]

	function toArray() {
		$serialize = array();		//%	return value [array]
		$serialize['name'] = $this->name;
		$serialize['description'] = $this->description;
		$serialize['permissions'] = $this->permissions;
		$serialize['export'] = $this->export;
		$serialize['relationships'] = $this->relationships;
		return $serialize;
	}

}

?>
