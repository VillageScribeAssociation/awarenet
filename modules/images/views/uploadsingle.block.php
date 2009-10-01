<? /*
<h3>Your Picture</h3>
[[:images::single::refModule=%%refModule%%::refUID=%%refUID%%::size=width290:]]

<h3>Change Picture</h3>
<form enctype='multipart/form-data' action='/images/upload/' method='POST'>
      <b>File:</b>
      <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
      <input type='file' name='userfile' size='15' />
      <input type='submit' value='Upload' /><br/>
      <input type='hidden' name='action' value='uploadSingleImage' />
      <input type='hidden' name='refModule' value='%%refModule%%' />
      <input type='hidden' name='refUID' value='%%refUID%%' />
      <input type='hidden' name='category' value='%%category%%' />
      <input type='hidden' name='return' value='uploadsingle' />
</form>
*/ ?>