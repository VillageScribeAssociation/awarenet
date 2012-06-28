<? /*

<h2>Preset Customizations</h2>

<p>These are templates for changing awareNet's appearance.</p>

<form name='frmApplyPreset' method='POST' action='%%serverPath%%users/applypreset/'>
	<input type='hidden' name='to' value='self' />
	[[:users::selectpreset::varname=preset:]]
	<input type='submit' value='Apply &gt;&gt;' />
</form>

<h2>Background Image</h2>

<p>You can set your page background to any image which has been uploaded to awareNet.  To do
so, copy the image's alias into the box below and chose the size you'd like it scaled to.</p>

<form name='frmSetBackground' method='POST' action='%%serverPath%%users/setthemeprefs/'>
	<input type='text' name='background' value='%%background%%' />
	[[:images::selecttransform::varname=transform::default=full:]]
	<input type='submit' value='Change Background Image &gt;&gt;' />
</form>

<h2>Colors</h2>

<p>awareNet's theme is designed around a simple monochrome gradient, with a few colors to highlight
important elements.  You can change them to any colors you like, but be careful not to make
foreground and background colors the same, or you won't be able to see the text.</p>

<script src='%%serverPath%%modules/editor/js/jscolor.js'></script>
<form name='frmSetColors' method='POST' action='%%serverPath%%users/setthemeprefs/'>
<table noborder>
  <tr>
	<td><b>Darkest:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_darkest' value='%%theme.c.darkest%%' />
		<small>Background of top/site menu.</small>
	</td>
  </tr>
  <tr>
	<td><b>Dark:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_dark' value='%%theme.c.dark%%' />
		<small>Background of title boxes and footer.</small>
	</td>
  </tr>
  <tr>
	<td><b>Medium:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_medium' value='%%theme.c.medium%%' />
		<small>Background of some table cells.</small>
	</td>
  </tr>
  <tr>
	<td><b>Light:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_light' value='%%theme.c.light%%' />
		<small>Background of some table cells and the submenu.</small>
	</td>
  </tr>
  <tr>
	<td><b>Lighter:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_lighter' value='%%theme.c.lighter%%' />
		<small>Background of page widgets and some table cells.</small>
	</td>
  </tr>
  <tr>
	<td><b>Lightest:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_lightest' value='%%theme.c.lightest%%' />
		<small>Background of most content.</small>
	</td>
  </tr>
  <tr>
	<td><b>Links:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_link' value='%%theme.c.link%%' />
		<small>Ordinary <a href='#'>links</a>.</small>
	</td>
  </tr>
  <tr>
	<td><b>Background:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_background' value='%%theme.c.background%%' />
		<small>Behind your wallpaper image, if any.</small>
	</td>
  </tr>
  <tr>
	<td><b>Highlights:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_action' value='%%theme.c.action%%' />
		<small>Action boxes and block feet.</small>
	</td>
  </tr>
  <tr>
	<td><b>Text:</b></td>
    <td>
		<input type='text' class='color' size='10' name='theme_c_text' value='%%theme.c.text%%' />
		<small>Foreground text color.</small>
	</td>
  </tr>
  <tr>
	<td></td>
	<td><input type='submit' value='Change &gt;&gt;' /></td>
  </tr>
</table>
</form>

*/ ?>
