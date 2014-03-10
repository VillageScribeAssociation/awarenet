<?

//--------------------------------------------------------------------------------------------------
//*	end a chat discussion between two users
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('userUID', $_POST)) { $page->doXmlError('No userUID'); }
	if (false == array_key_exists('partnerUID', $_POST)) { $page->doXmlError('No partnerUID'); }

	$userUID = $_POST['userUID'];
	$partnerUID = $_POST['partnerUID'];

	if ($user->UID != $userUID) { $page->doXmlError('Not your chat.'); }

	//----------------------------------------------------------------------------------------------
	//	change state of all messages to dismissed
	//----------------------------------------------------------------------------------------------

	$sql = "UPDATE live_chat SET state='dismissed' "
		 . "WHERE (fromUID='" . $userUID . "' AND toUID='" . $partnerUID . "') "
		 . "OR (fromUID='" . $partnerUID . "' AND toUID='" . $userUID . "')";

	$kapenta->db->query($sql);

	echo "DISMISSED CHAT MESSAGES";

?>
