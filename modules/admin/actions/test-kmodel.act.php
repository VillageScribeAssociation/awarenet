<?

	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	TEST core KModule object
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

	//--------------------------------------------------------------------------------------------
	//	check user role
	//--------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//--------------------------------------------------------------------------------------------
	//	make a dummy kmodel object
	//--------------------------------------------------------------------------------------------
	$testXml = "<model>
		<name>Projects_Project</name>
		<name>This model represents user projects.</name>
		<permissions>
			<permission>show</permission>
			<permission>edit</permission>
			<permission>delete</permission>
			<permission>new</permission>
			<export>deleteproject</export>
			<export>newproject</export>
		</permissions>
		<relationships>
			<relationship>member</relationship>
			<relationship>admin</relationship>
			<relationship>creator</relationship>
		</relationships>
	</model>";

	$model = new KModel($testXml);

	echo "name: $name<br/>\n";
	echo "description: $description <br/>\n";

	foreach($model->permissions as $permission) { echo "permission: $permission <br/>\n"; }
	foreach($model->export as $export) { echo "export: $export <br/>\n"; }
	foreach($model->relationships as $relationship) { echo "relationship: $relationship <br/>\n"; }

	$xml = $model->toXml();
	$xml = str_replace("<", '&lt;', $xml);
	$xml = str_replace(" ", '&nbsp;', $xml);
	$xml = str_replace("\n", "<br/>\n", $xml);
	echo $xml;

?>
