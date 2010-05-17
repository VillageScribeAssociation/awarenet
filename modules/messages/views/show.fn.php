<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a message
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a message [string]
//opt: noreply - kill the reply button (yes|no) [string]

function messages_show($args) {
	$noreply = 'no';
	if (array_key_exists('UID', $args) == false) { return false; }

	$model = new Message($args['UID']);
	$ext = $model->extArray();

	$ext['replybutton'] = '';
	if ($ext['folder'] != 'outbox') {
		$action = "%%serverPath%%messages/reply/re_%%UID%%";
		$ext['replybutton'] = "<td><form name='sendReply' method='GET' action='" . $action . "'>"
							. "<input type='submit' value='Send Reply' /></form></td>";

	}
	if ((array_key_exists('noreply', $args)) && ($args['noreply'] == 'yes')) { $noreply = 'yes'; }
	if ('yes' == $noreply) { $ext['replybutton'] = ''; }

	$html = replaceLabels($ext, loadBlock('modules/messages/views/show.block.php'));

	return $html;
}


//--------------------------------------------------------------------------------------------------

?>

