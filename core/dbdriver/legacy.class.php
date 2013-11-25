<?php

//--------------------------------------------------------------------------------------------------
//*	this is a temporary object to provide older modules access to updated drivers
//--------------------------------------------------------------------------------------------------
//+	TODO: remove this when all modules use $kapenta to initialize db


class KDBAdminDriver {

	//----------------------------------------------------------------------------------------------
	//.	properties
	//----------------------------------------------------------------------------------------------

	var $dba;

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	
	function KDBAdminDriver() {
		global $kapenta;
		$this->dba = $kapenta->getDBAdminDriver();
	}

	//----------------------------------------------------------------------------------------------
	//.	methods (wrap dbd)
	//----------------------------------------------------------------------------------------------

	function getTableInstallStatus($dbSchema) {
		return $this->dba->getTableInstallStatus($dbSchema);
	}

	function schemaToHtml($dbSchema, $title = '') {
		return $this->dba->schemaToHtml($dbSchema, $title);
	}

}

?>
