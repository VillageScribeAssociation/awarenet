<?

//--------------------------------------------------------------------------------------------------
//|	creates an iFrame for adding or editing a poll
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a Kapenta module [string]
//arg: refModel - type of object which will own poll question [string]
//arg: refUID - UID of object which will own poll question [string]
//opt: width - width of iframe, default is 570 (integer) [string]
//opt: height - height of iframe, default is 200 (integer) [string]

function polls_addeditpoll($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $theme;	

	$html = '';				//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	if (false == $kapenta->moduleExists($args['refModule'])) { return '(no such module)'; }
	if (false == $kapenta->db->objectExists($args['refModel'], $args['refUID'])) { return '(not found)'; }

	if (false == array_key_exists('width', $args)) { $args['width'] = '570'; }
	if (false == array_key_exists('height', $args)) { $args['height'] = '400'; }

	$args['width'] = '' . (int)$args['width'];
	$args['height'] = '' . (int)$args['height'];

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/polls/views/addeditpoll.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
	
}

?>
