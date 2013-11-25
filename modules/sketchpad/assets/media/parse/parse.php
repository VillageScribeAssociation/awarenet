<?

//--------------------------------------------------------------------------------------------------
//*	disabled on awareNet pending security review
//--------------------------------------------------------------------------------------------------

/*

function isType($v,$d) {

	return(!in_array($v,Array('.','..','.DS_Store'))?filetype($d):false);

}

function renameFile() { $dir='/Library/WebServer/Documents/canvas/media/parse/';

	if($d1=opendir($dir)) {
	
		while(($file=readdir($d1))!==false) { if($type=isType($file,$dir2=$dir.$file)) {
			
			if($type=='dir' && $d2=opendir($dir2)) { $i=0;
			
				echo "filename: $file -- filetype: $type\n";
	
				while(($file2=readdir($d2))!==false) { if($type2=isType($file2,$dir2=$dir.$file.'/'.$file2)) {
			
					echo("mv '{$dir}{$file}/{$file2}' '{$dir}{$file}/{$i}.png'"); $i++;
			
				} }
				
				echo "'$file':$i,";
	
				closedir($d2);
	
		} } }
		
		closedir($d1);
	
	}
}

function readGlyph() { $dir='/Library/WebServer/Documents/canvas/media/glyph/';

	if($d1=opendir($dir)) { $z1=''; $z2='';
	
		while(($file=readdir($d1))!==false) { if($type=isType($file,$dir2=$dir.$file)) {
			
			if($type=='dir' && $d2=opendir($dir2)) { $i=0;

				while(($file2=readdir($d2))!==false) { if($type2=isType($file2,$dir2=$dir.$file.'/'.$file2)) { $i++; } }
				
				$z1.="'$file':$i,"; $z2.="'$file',";
	
				closedir($d2);
	
		} } }
		
		closedir($d1);
		
		echo("{$z1}\n{$z2}");
	
	}
}

renameFile();

*/

//	INTERFACE.JS
//	------
//	'stamp':{'Default':1,'Oldschool':19,'Splatter':35},

//	GUI.JS
//	------
//	'stamp':{type:'menu', val:['Default','Oldschool','Splatter']},

?>
