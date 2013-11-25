<?php

//-------------------------------------------------------------------------------------------------
//*	XML representation of public peer data
//-------------------------------------------------------------------------------------------------

	header("Content-type: application/xml");

	$xml = ''
	 . "<?xml version='1.0' encoding='UTF-8' ?>\n"
	 . "<peer>\n"
	 . "    <uid>" . $kapenta->registry->get('p2p.server.uid') . "</uid>\n"
	 . "    <name>" . $kapenta->registry->get('p2p.server.name') . "</name>\n"
	 . "    <url>" . $kapenta->registry->get('p2p.server.url') . "</url>\n"
	 . "    <pubkey>" . $kapenta->registry->get('p2p.server.pubkey') . "</pubkey>\n"
	 . "    <firewalled>" . $kapenta->registry->get('p2p.server.fw') . "</firewalled>\n"
	 . "</peer>\n";

	echo $xml;

?>
