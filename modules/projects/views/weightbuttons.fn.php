<?

//--------------------------------------------------------------------------------------------------
//|	render buttons to increment and decrement a section's weight
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Section object [string]
//opt: sectionUID - overrides UID if present [string]

function projects_weightbuttons($args) {
	global $user;
	global $theme;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('sectionUID', $args)) { $args['UID'] = $args['sectionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(section UID not given)'; }

	if (false == $user->authHas('projects', 'projects_section', 'edit', $args['UID'])) { 
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/weightbuttons.block.php');
	$html = $theme->replaceLabels($args, $block);
	return $html;
}

?>