<? /*

<script language='Javascript'>

	function users_userSearch() {
		var theQ = document.getElementById('users_USQ');
		var blockTag = '[[' + ':users::searchresults'
			 + '::q=' + kutils.base64_encode(theQ.value)
			 + '::b64=yes'
			 + '::cbjs=%%cbjs%%'
			 + '::cblabel=%%cblabel%%'
			 + '::cbicon=%%cbicon%%'
			 + ':]]';

		klive.bindDivToBlock('userSearchResults', blockTag, false);
	}

	function users_userSearchKU() {
		users_userSearch();
	}

</script>

<br/>

<table noborder with='100%'>
  <tr>
    <td><input type='text' name='q' id='users_USQ' value='' onkeyup='users_userSearchKU();' style='width: 100%;' /></td>
    <td><input type='button' value='&gt;&gt;' onClick="users_userSearch();" /></dt>
  </tr>
</table>
<div id='userSearchResults'></div>

*/ ?>
