<?php

//--------------------------------------------------------------------------------------------------
//* Object to manage generic work queues
//--------------------------------------------------------------------------------------------------
//+ This is a planned object to allow IPC on a variety of platforms, using only the native
//+ filesystem, allowing operation on Android and windows.

class KQueue {

    //----------------------------------------------------------------------------------------------
    //. send a message to a named queue
    //----------------------------------------------------------------------------------------------
    //arg: $queue - name of a queue [string]
    //arg: $msg - message to add to the queue [string]
    //arg: $ttl - lifetime of this message if not received [string]

    function send($queue, $msg, $ttl) {
        //  TODO                
    }

    //----------------------------------------------------------------------------------------------
    //. receive a message from a queue
    //----------------------------------------------------------------------------------------------

    function recieve($queue) {
        //  TODO
    }

}


?>
