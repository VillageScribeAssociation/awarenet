<? /*
<b>Confirm: you wish to delete this theme preset?</b><br/>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%users/deletepreset/'>
    <input type='hidden' name='action' value='deleteObject' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='cancelDelete' method='POST' action='/users/themepresets/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>

[[:users::showpreset::presetUID=%%UID%%:]]

*/ ?>
