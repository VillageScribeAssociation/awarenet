<?php

//--------------------------------------------------------------------------------------------------
//*  temporary / migration method to help clean up globals in code
//--------------------------------------------------------------------------------------------------

    if ('admin' !== $kapenta->user->role) { $kapenta->page->do404(); }

    $files = $kapenta->fs->search('modules/', '.php', false);

    foreach($files as $file) {
        echo "file: $file <br/>\n";
    
        $raw = $kapenta->fs->get($file);
        $changed = false;
        $lines = explode("\n", $raw);
        
        foreach($lines as $line) {

            $indent = ltrim($line);
            $cline = trim($line);

            $fixline = '';

            if (('global ' === substr($cline, 0, 7)) && (false !== strpos($cline, ','))) {
                
                echo $cline . "<br/>\n";
                $cline = str_replace(';', '', $cline);
                $cline = str_replace('global ', '', $cline);

                $globs = explode(',', $cline);

                echo "Found " . count($globs) . " globals.<br/>\n";
    
                foreach($globs as $glob) {
                    $glob = trim($glob);
                    echo "global $glob;<br/>\n";
                    $fixline = $fixline . "\t\tglobal $glob;\n";                 
                }

                $raw = str_replace($line, $fixline, $raw);
                $changed = true;

            }

        }

        if (true == $changed) {

            echo "Replacing file: $file<br/><textarea rows='10' style='width: 100%'>$raw</textarea><br/>";

            $kapenta->fs->put($file, $raw);

        }

    }

?>
