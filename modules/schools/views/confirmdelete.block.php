<? /*
<b>Confirm: you wish to remove this school from the network?</b><br/>
<p>Note that this action is not recommended, users and all their content depend on schools as their top level object, and these dependant objects will be deleted or become inacessable.  If you simply wish the schoool to disppear from listings, it may be hidden from the scheool's edit page.</p>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%schools/delete/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: I know what Im doing' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%schools/%%raUID%%/'>
    <input type='submit' value='No: Cancel (click this one, seriously)' />
    </form>
    </td>
  </tr>
</table>
*/ ?>
