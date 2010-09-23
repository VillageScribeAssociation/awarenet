<? /*
<module>
    <modulename>images</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Module for uploading, managing and displaying images.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <search>no</search>
    <dependencies>
		<model>aliases</model>
    </dependencies>
    <models>
        <model>
            <name>Image</name>
            <description>Object representing user images</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
                <export>images-add</export>
                <export>images-edit</export>
                <export>images-show</export>
                <export>images-remove</export>
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
