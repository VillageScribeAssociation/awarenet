<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');

//--------------------------------------------------------------------------------------------------
//|	displays the UID of a Tags_Tag object given its name
//--------------------------------------------------------------------------------------------------
//arg: name - name of a tag [string]

function tags_uid($args) {
	$uid = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments (no permissions required for this)
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('name', $args)) { return $uid; }

	//----------------------------------------------------------------------------------------------
	//	look up the tag
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Tag($args['name'], true);
	if (false == $model->loaded) { return $uid; 
	$uid = $model->UID;

	return $uid;
}

?>
