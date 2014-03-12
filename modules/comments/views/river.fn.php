<?php

//--------------------------------------------------------------------------------------------------
//|	show comments list in river view
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object to which comments may be attached [string]
//arg: refUID - UID of object to which comments may be attached [string]

function comments_river($args) {
	global $kapenta;

	$num = '10';						//%	default number of threads per page [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	if (true == array_key_exists('num', $args)) { $num = $args['num']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = ''
	 . "[[:live::river"
	 . "::mod=comments"
	 . "::view=list"
	 . "::pv=pageNo"
	 . "::allow=num|refModel|refModule|refUID"
	 . "::refModule=" . $args['refModule']
	 . "::refModel=" . $args['refModel']
	 . "::refUID=" . $args['refUID']
	 . "::num=" . $num
	 . ":]]";

	return $html;
}

?>
