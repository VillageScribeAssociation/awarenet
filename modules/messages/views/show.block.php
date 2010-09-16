<? /*
<table noborder class='wireframe'>
  <tr>
    <td><b>From:</b></td>
    <td><b>To:</b></td>
  </tr>
  <tr>
    <td>[[:users::summarynav::userUID=%%fromUID%%:]]</td>
    <td>[[:users::summarynav::userUID=%%toUID%%:]]</td>
  </tr>
</table>
<h2>Subject: %%messageTitle%%</h2>
%%content%%<br/>
<small>sent: %%createdOn%%</small><br/>
<table noborder>
  <tr>
    <td>
      <form name='deleteMessage' method='POST' action='%%serverPath%%messages/delete/'>
	  <input type='hidden' name='action' value='deleteMessage' />
	  <input type='hidden' name='UID' value='%%UID%%' />
      <input type='submit' value='Delete' />
      </form>
    </td>
	%%replybutton%%
  </tr>
</table>
*/ ?>
