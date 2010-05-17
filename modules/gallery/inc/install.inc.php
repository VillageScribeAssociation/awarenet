<?

//--------------------------------------------------------------------------------------------------
//	install scripts for gallery module
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	install the gallery module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function gallery_install_module() {
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	$report .= "<h3>Installing Gallery Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	create gallery table if it does not exist, upgrade it if it does
	//------------------------------------------------------------------------------------------
	$model = new Gallery();
	$dbSchema = $model->initDbSchema();
	$report .= dbInstallTable($dbSchema);	

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//returns: HTML installation report [string]
// if installed correctly report will contain HTML comment <!-- installed correctly -->

function gallery_install_status_report() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	//---------------------------------------------------------------------------------------------
	//	ensure that the gallery table exists and is correct
	//---------------------------------------------------------------------------------------------
	$installed = true;
	$model = new Gallery();
	$dbSchema = $model->initDbSchema();

	if (dbTableExists($dbSchema['table']) == true) {
		//-----------------------------------------------------------------------------------------
		//	table present
		//-----------------------------------------------------------------------------------------
		$extantSchema = dbGetSchema($dbSchema['table']);

		if (dbCompareSchema($dbSchema, $extantSchema) == false) {
			//-------------------------------------------------------------------------------------
			// table schemas DO NOT match (fail)
			//-------------------------------------------------------------------------------------
			$installed = false;		
			$report .= "<p>A '" . $dbSchema['table'] . "' table exists, but does not match "
					 . "object's schema.</p>\n"
					 . "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n"
					 . "<b>Extant Table:</b><br/>\n" . dbSchemaToHtml($extantSchema) . "<br/>\n";

		} else {
			//-------------------------------------------------------------------------------------
			// table schemas match
			//-------------------------------------------------------------------------------------
			$report .= "<p>'gallery' table exists, matching object schema.</p>";
			$report .= "<b>Database Table:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";

		}

	} else {
		//-----------------------------------------------------------------------------------------
		//	table missing (fail)
		//-----------------------------------------------------------------------------------------
		$report .= "<p>'gallery' table does not exist in the database.</p>";
		$report .= "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";
		$installed = false;
	}

	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

//-------------------------------------------------------------------------------------------------
//	deprecated	// TODO: remove
//-------------------------------------------------------------------------------------------------

function install_gallery_module() {
	$model = new Gallery();
	$report = $model->install();
	return $report;
}

?>
