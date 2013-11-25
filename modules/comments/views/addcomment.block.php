<? /*
<div class='spacer'></div>
<form
	id='frmAddComment'
	name='addComment'
	method='POST'
	action='%%serverPath%%comments/add/'
	onSubmit="khta.updateAllAreas();"
>

<input type='hidden' name='refModule' value='%%refModule%%' />
<input type='hidden' name='refModel' value='%%refModel%%' />
<input type='hidden' name='refUID' value='%%refUID%%' />
<input type='hidden' name='return' value='%%return%%' />

[[:comments::tip_policy:]]
[[:editor::add::name=comment::height=200:]]

<!-- <textarea name='comment' rows='7' cols='50' style='width: 100%;'></textarea><br/> -->
<input type='submit' value='Add Comment' />
</form>

*/ ?>
