<?php

//--------------------------------------------------------------------------------------------------
//  object to read and write objects given a GUID
//--------------------------------------------------------------------------------------------------

class Store_IO {

    var $baseDir = 'data/store/';
    var $journal = 'data/store/journal.txt';
    var $depth = 4;

    //----------------------------------------------------------------------------------------------
    //. constructor
    //----------------------------------------------------------------------------------------------

    function Store_IO() {
            
    }

    //----------------------------------------------------------------------------------------------
    //. load an object
    //----------------------------------------------------------------------------------------------
    // arg: guid - UID of the object to retrieve

    function get($guid) {
        $fileName = $this->guidToFilename($guid);
        if (false === $kapenta->fs->exists($fileName)) { return ''; }
        $jsonStr = $kapenta->fs->get($fileName);
    }

    //----------------------------------------------------------------------------------------------
    //. save an object
    //----------------------------------------------------------------------------------------------
    //returns: empty string on success, error message on failure

    function put($guid, $jsonObj) {

        $fileName = $this->guidToFilename;
        $jsonStr = JSON_stringify($jsonObj);
        $journalLine = 'a:' . $guid . ':' $kapenta->date() . str_repeat(' ', 100);
        $journalLine = substr($journalLine, 0, 99) . "\n";

        $check = true;

        //  check that this does not already exist
        if (true === $kapenta->fs->exists($fileName)) {
            return 'An object with this UID already exists.';
        }

        $check = $kapenta->fs->makePath($fileName)
        if (false === $check) { return 'Could not create path to store this object.'; }

        //  write a lock
        //TODO

        //  write the file
        $check = $kapenta->fs->put($fileName, $jsonStr, true);
        if (false === $check) { return 'Could not store object ' . $guid . ' on disk.'; }

        //  write to journal
        $kapenta->fs->put($journal, $journalLine, true, false, 'a+');
        if (false === $check) { 
            return 'Could not add object ' . $guid . ' to the journal.'; 
        }

        //  add to type list
        $check = $this->addToTypeCatalogue($jsonObj->type, $guid);
        if (false === $check) { return 'Could not add ' . $guid . ' to type catalogue: ' . $jsonObj->type; }

        //  add to any relationship lists
        if ($jsonObj->extend === 'relationship')) {
            $this->recordRelationship($jsonObj);
        }

        //  update any indexes
        //TODO

        //  delete the lock
        //TODO

        return true;
    }

    //----------------------------------------------------------------------------------------------
    //. add to type catalogue
    //----------------------------------------------------------------------------------------------

    function addToTypeCataloge($type, $guid) {
        $fileName = $baseDir . '/bytype/' . $type . '.txt';
        $check = $kapenta->fs->put($fileName, $guid . "\n", true, false, 'a+');
        return $check;
    }

    function recordRelationship($guid, $jsonObj) {
        $fileName = $this->guidToFilename($jsonObj->fromguid);
        $fileName = str_replace('.json', '.rel');
        $relType = str_replace('.relationship', '', $jsonObj->type);        
        $newLine = $guid . '|' . $relType . '|' . $jsonObj->toguid . "\n";
        $kapenta->fs->put($fileName, $newLine, true, false, 'a+');
    }

    //----------------------------------------------------------------------------------------------
    //. make a filename given a GUID
    //----------------------------------------------------------------------------------------------

    function guidToFilename($guid) {
        $fileName = $baseDir;
        for ($i = 0; $i < $this->depth; $i++) {
            $fileName .= substr($guid, $i, 1) . '/';
        }
        $fileName .= $guid . '.json';
        return $fileName;
    }

}

?>
