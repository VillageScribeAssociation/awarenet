<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_show($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Calendar_Entry($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/calendar/views/show.block.php');
	$labels = $model->extArray();
	$labels['userLink'] = '<b>Created By:</b> [[:users::namelink::raUID=' . $labels['createdBy'] . ':]]';
	$labels['venueString'] = '<b>Venue:</b> ' . $labels['venue'];
	$labels['eventStartString'] = '<b>Starting:</b> ' . $labels['eventStart'];
	$labels['eventEndString'] = '<b>Ending:</b> ' . $labels['eventEnd'];


	if ('' == trim($labels['venue'])) { $labels['venueString'] = ' '; }
	if ('00:00' == trim($labels['eventStart'])) { $labels['eventStartString'] = ' '; }
	if ('00:00' == trim($labels['eventEnd'])) { $labels['eventEndString'] = ' '; }

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
