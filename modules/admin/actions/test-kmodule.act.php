<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	TEST core KModule object
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

	//--------------------------------------------------------------------------------------------
	//	check user role
	//--------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $page->do403(); }

	//--------------------------------------------------------------------------------------------
	//	make a dummy kmodel object
	//--------------------------------------------------------------------------------------------
	//$fileName = 'modules/users/module.xml.php';
	//$testXml = $kapenta->fileGetContents($fileName);

	$module = new KModule('forums');

	echo "name: {$module->modulename}<br/>\n";
	echo "description: {$module->description}<br/>\n";

	foreach($module->defaultpermissions as $defPerm) { echo "permision: " . $defPerm . "<br/>\n"; }

	$xml = $module->toXml();
	$xml = str_replace('<', '&lt;', $xml);
	$xml = str_replace('>', '&gt;', $xml);
	//$xml = str_replace("\n", "<br/>\n", $xml);
	echo "<pre>$xml</pre>";

?>
