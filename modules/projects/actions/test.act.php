<?

//--------------------------------------------------------------------------------------------------
//	testing xml class for CDATA
//--------------------------------------------------------------------------------------------------

	$xml = "<section><title>This is a test</title>
<content><![CDATA[this is <b>escaped</b> html]]></content>
</section>";

	echo "<textarea rows='5' cols='80'>$xml</textarea><br/>\n";

	$xe = new XmlEntity($xml);

	echo "xe->type: " . $xe->type . "<br/>\n";
	foreach($xe->children as $child) {
		echo "child->type: " . $child->type . "<br/>\n";
		if ($child->cdata == FALSE) { 
			echo "child->cdata: FALSE<br/>\n";
		} else {
			echo "child->cdata: TRUE<br/>\n";
		}
		echo "child->content: " . $child->toString() . "<br/>\n";
	}

	foreach($xml_cdata_set as $id => $content) {
		echo "xml_cdata_set[" . $id . "]: $content<br/>\n";
	}

?>
