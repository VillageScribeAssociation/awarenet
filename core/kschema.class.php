<?

//--------------------------------------------------------------------------------------------------
//*	represents object store / database table schema
//--------------------------------------------------------------------------------------------------

class KSchema {
	
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $module = '';		//_	kapenta module to which objects of this type belong [string]
	var $model = '';		//_	type of object to be stored and name of table [string]
	var $table = '';		//_	name of kapenta table where objects will be stored [string]
	var $fields;			//_	array of fieldname => data type [array]
	var $indices;			//_	array of fieldname => index size [array]
	var $comments;			//_	array of fieldnames => comments [array]
	var $nodiff;			//_	array of fieldnames which don't trigger new revisions [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a kapenta module [string]
	//arg: model - name of a model / object type [string]

	function KSchema($module, $model) {
		$this->module = $module;
		$this->model = $model;
		$this->fields = array();
		$this->indices = array();
		$this->nodiff = array();
	}

}

?>
