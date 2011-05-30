<? /*
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <modulename>live</modulename>
    <version>0</version>
    <revision>0</revision>
    <description>Interface to live server and page AJAX functions.</description>
    <core>no</core>
    <dependencies>
    </dependencies>
    <models>
        <model>
            <name>Chat</name>
            <description>Temporary chat buffer for ongoing conversations.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
        <model>
            <name>Mailbox</name>
            <description>Stores messages for AJAX clients.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
        <model>
            <name>Trigger</name>
            <description>Event triggers for AJAX clients to receive updates.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
    </models>
    <defaultpermissions>
    </defaultpermissions>
</module>
*/ ?>
