<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//	just show a tag name, optionally with link to
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID ro alias of a Tags_Tag object [string]
//opt: tagUID - oveerrides raUIDis present [string]
//opt: link - link to list of items sharing this tag, default is no (yes|no|module) [string]
//opt: module - name of a kapenta module [string]

function tags_name($args) {
	global  $db, $user, $theme, $kapenta;
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

	if (true == array_key_exists('link', $args)) { $link = $args['link']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = $model->name;

	if ('yes' == $link) { 
		$showUrl = "%%serverPath%%tags/show/" . $model->name;
		$html = "<a href='" . $showUrl . "'>" . $model->name . "</a>"; 
	}

	// some modules may provide their own tag interface
	if ('module' == $link) { 
		if (false == array_key_exists('module', $args)) { return '(no module)'; }
		if (false == $kapenta->moduleExists($args['module'])) { return '(no such module)'; }
		$showUrl = "%%serverPath%%" . $args['module'] .  "/tag/" . $model->name;
		$html = "<a href='" . $showUrl . "'>" . $model->name . "</a>"; 
	}

	return $html;
}

?>
