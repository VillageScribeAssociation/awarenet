<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	subnav for day display
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]

function calendar_yearsubnav($args) {
		global $kapenta;
		global $user;
		global $db;
		global $theme;

	$html = '';
	$year = '1970';
	$day = '01';
	$month = '01';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('year', $args)) { return ''; }
	$year = $db->addMarkup($args['year']);
	//TODO: permissions check here
	
	$model = new Calendar_Entry();		//TODO: replace with utility/collection class

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['prevYearUrl'] = $kapenta->serverPath . 'calendar/list/year_' . ($args['year'] - 1);
	$next = $model->getNextDay($day, $month, $year);
	$labels['nextYearUrl'] = $kapenta->serverPath . 'calendar/list/year_' . ($args['year'] + 1);
		
	$block = $theme->loadBlock('modules/calendar/views/yearsubnav.block.php');
	$html .= $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
