<? /*
<b>Confirm: you wish to delete this folder and everything in it?</b><br/>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%folder/delete/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='/folder/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>

*/ ?>
