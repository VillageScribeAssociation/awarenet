<? /*
[[:theme::navtitlebox::width=570::label=Confirm:]]
<div class='inlinequote'>
Confirm: you wish to empty the sync queue?  This will delete all outgoing messages waiting to be sent or retried.<br/><br/>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%sync/clearqueue/confirm_all/'>
    <input type='submit' value='Yes: Empty it' />
    </form>
    </td>
    <td valign='top'>
    <form name='cancelThis' method='POST' action='%%serverPath%%sync/showqueue/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
</div>
*/ ?>
