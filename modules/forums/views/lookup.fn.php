<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get a link, title or URL to an object owned by this module
//--------------------------------------------------------------------------------------------------
//arg: model - type of object we are looking up [string]
//arg: UID - type of object we are looking up [string]
//opt: link - create full link or just URL, default is yes (yes|no) [string]

function forums_lookup($args) {
	$html = '';						//%	return value [string]
	$link = 'yes';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return '(model not given)'; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }

	//----------------------------------------------------------------------------------------------
	//	look up the object
	//----------------------------------------------------------------------------------------------
	switch(strtolower($args['model'])) {
		
		case 'forums_thread':
			$model = new Forums_Thread($args['UID']);
			if (false == $model->loaded) { return '(thread not found)'; }
			$html = "%%serverPath%%forums/showthread/" . $model->alias;
			if ('yes' == $link) { $html = "<a href='$html'>" . $model->title . "</a>"; }
			break;

	}

	return $html;
}

?>
