<?

//--------------------------------------------------------------------------------------------------
//	testing sync->bas64DecodeSql();
//--------------------------------------------------------------------------------------------------

$xml = "<update>
	<table>Aliases_Alias</table>
	<fields>
		<UID>MTg4OTgwNDMyMDEzOTk0NzY1</UID>
		<refModule>aW1hZ2Vz</refModule>
		<refModel>SW1hZ2VzX0ltYWdl</refModel>
		<refUID>ODU0MTgyMDcyMTYyMTc5ODk5</refUID>
		<aliaslc>MzAxNjIzNDUxNC0wNmJjNmY0YTY2LWIuanBn</aliaslc>
		<alias>MzAxNjIzNDUxNC0wNmJjNmY0YTY2LWIuanBn</alias>
		<createdOn>MjAxMC0wOC0wMiAwMjozNjo0MA==</createdOn>
		<createdBy></createdBy>
		<editedOn>MjAxMC0wOC0wMiAwMjozNjo1Ng==</editedOn>
		<editedBy>YWRtaW4=</editedBy>
	</fields>
</update>";

	$data = $sync->base64DecodeSql($xml);
	print_r($data);

?>
