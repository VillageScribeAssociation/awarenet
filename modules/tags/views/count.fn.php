<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the number of objects with a given tag, sometimes called its magnitude
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID ro alias of a Tags_Tag object [string]
//opt: tagUID - oveerrides raUIDis present [string]
//opt: link - link to list of items sharing this tag, default is no (yes|no) [string]

function tags_count($args) {
		global $kapenta;
		global $user;
		global $theme;

	$html = '';			//%	return value [string]
	$link = 'no';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('tagUID', $args)) { $args['raUID'] = $args['tagUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Tags_Tag($args['raUID']);
	if (false == $model->loaded) { return '(not found)'; }
	//TODO: check permissions here

	if ((true == array_key_exists('link', $args)) && ('yes' == $args['link'])) { $link = 'yes'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = $model->objectCount;
	if ('yes' == $link) { 
		$showUrl = "%%serverPath%%tags/show/" . $model->name;
		$html = "<a href='" . $showUrl . "'>" . $model->objectCount . "</a>"; 
	}

	return $html;
}

?>
