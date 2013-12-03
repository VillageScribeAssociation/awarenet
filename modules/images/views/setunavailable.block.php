<? /*
<h2>Set 404/Unavailable Image</h2>
<p>This sets the placeholder image which is shown when a requested image is not available on the local site, becuse it has not yet been syncs to this server or is otherwise inacessible.</p>
[[:images::unavailable::size=width570:]]<br/>
<form name='setUnavailable' method='POST' action='%%serverPath%%images/setunavailable/'>
	<input type='hidden' name='action' value='setUnavailable' />
	<b>UID or alias of an image: </b>
	<input type='text' name='UID' value='' size='20' />
	<input type='submit' value='Change' />
</form>
*/ ?>
