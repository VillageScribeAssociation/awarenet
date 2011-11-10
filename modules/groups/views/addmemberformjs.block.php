<? /*

<div class='inlinequote'>
<table noborder width='100%'>
  <tr>
    <td><b>Role:</b></td>
    <td>
      <select name='position%%userUID%%' id='selPosition%%userUID%%'>
        <option value='member'>member</option>
        <option value='president'>president</option>
        <option value='vice-president'>vice-president</option>
        <option value='secretary'>secretary</option>
        <option value='treasurer'>treasurer</option>
        <option value='captain'>captain</option>
        <option value='vice-captain'>vice-captain</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b>Admin:</b></td>
    <td>
      <select name='admin%%userUID%%' id='selAdmin%%userUID%%'>
        <option value='no'>no</option>
        <option value='yes'>yes</option>
      </select>
    </td>
  </tr>
  <tr>
    <td></td>
    <td><input 
			type='button' 
			value='Add to group' 
			onClick="memberConsole.addMember(
				'%%userUID%%', 
				document.getElementById('selPosition%%userUID%%').value,
				document.getElementById('selAdmin%%userUID%%').value
			);"
		/>
	</td>
  </tr>
</table>
</div>
*/ ?>
