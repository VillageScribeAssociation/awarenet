<?

//--------------------------------------------------------------------------------------------------
//|	select box for choosing whether a blog post is published or not
//--------------------------------------------------------------------------------------------------
//arg: value - (yes|no) [string]

function moblog_selectpublished($args) {
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('value', $args)) { return '(value not given)'; }

	if ('yes' == $args['value']) {
		$html = ''
		 . "<select name='published'>\n"
		 . "  <option value='yes'>Yes - everyone can see my post</option>\n"
		 . "  <option value='no'>No - only I can see my post</option>\n"
		 . "</select>\n";
	} else {
		$html = ''
		 . "<select name='published'>\n"
		 . "  <option value='no'>No - only I can see my post</option>\n"
		 . "  <option value='yes'>Yes - everyone can see my post</option>\n"
		 . "</select>\n";
	}

	return $html;
}

?>
