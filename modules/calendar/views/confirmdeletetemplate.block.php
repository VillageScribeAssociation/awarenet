<? /*
<b>Confirm: you wish to delete this template calendar entry?</b><br/>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%calendar/deletetemplate/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%calendar/edittemplate/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
*/ ?>
