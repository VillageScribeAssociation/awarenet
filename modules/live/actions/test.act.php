<?

//--------------------------------------------------------------------------------------------------
//*	test of getblock
//--------------------------------------------------------------------------------------------------

	$testContents = "
<script language='Javascript'>

	function testGBMChange() {
		var theTxt = document.getElementById('txtGBMPlain');
		var theB64 = document.getElementById('txtGBMB64');
		theB64.value = base64_encode(theTxt.value);
	}

	function testGBXChange() {
		var theTxt = document.getElementById('txtGBXPlain');
		var theB64 = document.getElementById('txtGBXB64');
		theB64.value = base64_encode(theTxt.value);
	}

	function testXHRPost() {
		var theTxt = document.getElementById('txtGBXPlain');
		var theB64 = document.getElementById('txtGBXB64');
		theB64.value = base64_encode(theTxt.value);
	
		var params = 'b=' + escape(theB64.value);
		//alert('sending: ' + params);

		var req = new XMLHttpRequest();  
		req.open('POST', 'http://kapenta.co.za/live/getblock/', true);  
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		//req.setRequestHeader('Content-length', params.length);
		req.setRequestHeader('Connection', 'close'); 
		req.onreadystatechange = function (aEvt) {  
			var theTa = document.getElementById('taXHR');
			theTa.value = 'loading: ' + req.status;
			if ((4 == req.readyState) && (200 == req.status))  {
				theTa.value = req.responseText; 
			}
		}
		req.send(params);
	}

</script>

<h2>/getblock/ - Manual Post</h2>

<form name='testGetblockManual' method='POST' action='%%serverPath%%live/getblock/'>
<table noborder>
  <tr>
    <td><b>Plain:</b></td>
    <td><input type='text' name='p' id='txtGBMPlain' value='[~[:blog::summary::UID=932122408179773414:]~]'
			onChange=\"testGBMChange();\" size='50'></td>
  </tr>
  <tr>
    <td><b>base64:</b></td>
    <td><input type='text' name='b' id='txtGBMB64' size='50'></td>
  </tr>
</table>
<input type='submit' value='POST' />
</form>

<h2>/getblock/ - XmlHTTPRequest POST</h2>

<form name='testGetblockXHR' method='POST' action='#'>
<table noborder>
  <tr>
    <td><b>Plain:</b></td>
    <td><input type='text' name='xp' id='txtGBXPlain' value='[~[:blog::summary::UID=932122408179773414:]~]'
			onChange=\"testGBXChange();\" size='50'></td>
  </tr>
  <tr>
    <td><b>base64:</b></td>
    <td><input type='text' name='xb' id='txtGBXB64' size='50'></td>
  </tr>
  <tr>
    <td><b>XHR:</b></td>
    <td><textarea rows='10' cols='50' id='taXHR'></textarea></td>
  </tr>
</table>
<input type='button' value='POST' onClick='testXHRPost();' />
</form>

";

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/live/actions/test.page.php');
	$kapenta->page->blockArgs['testcontents'] = $testContents;
	$kapenta->page->render();

?>
