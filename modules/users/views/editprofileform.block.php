<? /*

<form name='editprofile'
	id='formEditProfile'
	method='POST'
	action='%%serverPath%%users/saveprofile/'
	onSubmit="formCheckRemove('formEditProfile');" />

<input type='hidden' name='action' value='saveProfile' />
<input type='hidden' name='UID' value='%%UID%%' />

<b>Birth Year</b>
<input type='text' name='birthyear' size='4' value='%%birthyear%%'> <small>When were you born (yyyy)?</small>
<br/><br/>

<b>Interests</b>
<small>what are you interested in?</small>
<textarea name='interests' rows='5' cols='70' style='width: 100%;'>%%interests%%</textarea><br/><br/>

<b>Home Town</b>
<small>where are you from?</small>
<input type='text' name='hometown' value='%%hometown%%' size='50' style='width: 100%;' /><br/><br/>

<b>Goals</b>
<small>what do you want to do with your life?</small>
<textarea name='goals' rows='5' cols='70' style='width: 100%;'>%%goals%%</textarea><br/><br/>

<b>Music</b>
<small>what music do you like?</small>
<textarea name='music' rows='5' cols='70' style='width: 100%;'>%%music%%</textarea><br/><br/>

<b>Books</b>
<small>how about books?</small>
<textarea name='books' rows='5' cols='70' style='width: 100%;'>%%books%%</textarea><br/><br/>

<b>Also</b>
<small>anything else you'd like to say about yourself...</small>
<textarea name='also' rows='5' cols='70' style='width: 100%;'>%%also%%</textarea><br/><br/>
<input type='submit' value='Save Changes to Profile' />
<hr/>
<br/>

[[:theme::navtitlebox::label=Contact Details:]]
<table noborder>
  <tr>
    <td><b>Email:</b></td>
    <td><input type='text' name='email' size='40' value='%%email%%' /></td>
  </tr>
  <tr>
    <td><b>Phone No:</b></td>
    <td><input type='text' name='tel' size='40' value='%%tel%%' /></td>
  </tr>
</table>

<input type='submit' value='Save Changes to Profile' />
</form>
<hr/>

<script language='Javascript'>
	formCheckAdd('formEditProfile');
</script>

*/ ?>
