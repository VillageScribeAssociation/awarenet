<?php

//--------------------------------------------------------------------------------------------------
//*	test local memcached installation
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }
	error_reporting(E_ALL & ~E_NOTICE);

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	$allOk = true;

	try {

		$mc = new Memcached();

	} catch(Exception $e) {
		echo "Memcached not available on this server.";
		$allOk = false;
	}

	if (true == $allOk) {
		$mc->addServer("localhost", 11211);

		$mc->set("foo", "Test value 1");
		$mc->set("bar", "Test value 2");

		if (
			("Test value 1" == $mc->get("foo")) &&
			("Test value 2" == $mc->get("bar"))  
		) {
			
			echo "<div class='chatmessagegreen'>Memcached working as expected.</div>";

			if (true == $mc->getOption(Memcached::OPT_COMPRESSION)) {
				echo "<div class='chatmessageblack'>Compression enabled.</div>";
			} else {
				echo "<div class='chatmessageblack'>Compression disabled.</div>";
			}

		} else {

			echo "<div class='chatmessagered'>Memcached not working.</div>";

		}



	}

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
