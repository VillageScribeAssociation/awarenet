<?


//--------------------------------------------------------------------------------------------------
//*	developing for unordered lists
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

$testData = "
* test
* data
";

$lines = explode("\n", $testData);
foreach($lines as $line) {

	if ('*' == substr($line, 0, 1)) {}
}

echo "something";

?>
