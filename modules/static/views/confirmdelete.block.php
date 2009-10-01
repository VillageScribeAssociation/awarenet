<? /*
<div class='inlinequote'>
<b>Confirm: do you wish to delete this page?</b>
<table noborder>
  <tr>
    <td valign='top'>
      <form name='confirmDelete' method='POST' action='/static/delete/'>
      <input type='hidden' name='action' value='deleteStaticPage' />
      <input type='hidden' name='UID' value='%%UID%%'>
      <input type='submit' value='Confirm'>
      </form>
    </td>
    <td>
      <form name='cancelDelete' method='POST' action='/static/%%recordAlias%%'>
      <input type='submit' value='Cancel'>
      </form>
    </td>
  </tr>
</table>
</div>
*/ ?>