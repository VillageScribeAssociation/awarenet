<? /*

	<!-- basic / fallback upload form, used where javascript is disabled or browser very old -->
	<form name='frmLegacyUpload' enctype='multipart/form-data' action='%%serverPath%%live/upload/' method='POST'>
		<b>File:</b>
		<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
		<input type='file' name='userfile' size='20' />
		<input type='submit' value='Upload' /><br/>
		<input type='hidden' name='action' value='upload' />
		<input type='hidden' name='refModule' value='%%refModule%%' />
		<input type='hidden' name='refModel' value='%%refModel%%' />
		<input type='hidden' name='refUID' value='%%refUID%%' />
		<input type='hidden' name='tags' value='%%tags%%' />
		<input type='hidden' name='return' value='uploadmultiple' />
	</form>

*/ ?>
