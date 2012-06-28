<?

//--------------------------------------------------------------------------------------------------
//|	makes an XML snippet detailing local room memberships to be sent to central server
//--------------------------------------------------------------------------------------------------
//TODO: consider security implcations of having this a publicly available block
//TODO: move to client.class.php

function chat_localmembersxml($args) {
	global $db;	
	$xml = '';		//%	return value [string]		

	$conditions = array();
	$range = $db->loadRange('chat_membership', '*', $conditions, 'room, user')

	$xml .= "<sl>";
	foreach($range as $item) {
		$xml .= "\t<room>" . $item['room'] . "</room><user>" . $item['user'] . "</user>\n";
	}
	$xml .= "</sl>";

	return $xml;
}

?>
