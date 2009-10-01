<? /*
[[:theme::navtitlebox::width=570::label=Confirm:]]
<div class='inlinequote'>
Confirm: you wish to delete this record?<br/><br/>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='/calendar/delete/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='/calendar/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
</div>
*/ ?>