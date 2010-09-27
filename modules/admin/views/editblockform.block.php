<? /*

<h2>edit block</h2>
<form name='editBlock' method='POST' action='%%serverPath%%admin/saveblock/'>
<input type='hidden' name='action' value='saveBlock' />
<input type='hidden' name='module' value='%%refModuleName%%' />
<input type='hidden' name='block' value='%%refBlockName%%' />

<textarea id='taBlockContentJs' rows='20' cols='80' name='blockContent'></textarea><br/>
<script>
%%blockContentJs64%%
base64_loadTextArea('taBlockContentJs', blockContentJs64);
</script>

<table noborder>
  <tr>
    <td valign='top'>
      <input type='submit' value='save' />
      </form>
    </td>
    <td valign='top'>
      <form name='cancelEdit' action='admin/listpages/'>
      <input type='submit' value='cancel' />
    </td>
  </tr>
</table>

*/ ?>
