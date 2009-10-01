<h2>edit block</h2>
<form name='editBlock' method='POST' action='/blocks/save/'>
<input type='hidden' name='action' value='saveBlock' />
<input type='hidden' name='module' value='%%moduleName%%' />
<input type='hidden' name='block' value='%%blockName%%' />
<textarea rows='20' cols='80' name='blockContent'>%%blockContent%%</textarea><br/>
<table noborder>
  <tr>
    <td valign='top'>
      <input type='submit' value='save' />
      </form>
    </td>
    <td valign='top'>
      <form name='cancelEdit' action='/pages/list/'>
      <input type='submit' value='cancel' />
      </table>
    </td>
  </tr>
</table>