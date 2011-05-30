<? /*
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <modulename>tags</modulename>
    <version>0</version>
    <revision>0</revision>
    <description>describe your module here</description>
    <core>no</core>
    <dependencies>
    </dependencies>
    <models>
        <model>
            <name>Index</name>
            <description>Relates objects to tags</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
                <export>suggest</export>
                <export>tags-manage</export>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
        <model>
            <name>Tag</name>
            <description>Central organising object</description>
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
