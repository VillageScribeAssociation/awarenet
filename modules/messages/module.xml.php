<? /*
<module>
    <modulename>messages</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Personal messages, like on-site email.</description>
    <core>no</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <dependencies>
		<module>aliases</module>
		<module>images</module>
		<module>comments</module>
    </dependencies>

    <models>
        <model>
            <name>Messages_Message</name>
            <description>A copy of a PM.</description>
            <permissions>
                <permission>send</permission>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>recipient</relationship>
            </relationships>
        </model>
    </models>

	<defaultpermissions>
		student:p|messages|messages_message|send
		student:c|messages|messages_message|delete|(if)|owner
		student:c|messages|messages_message|images-show
		student:c|messages|messages_message|images-add|(if)|creator
		student:c|messages|messages_message|images-remove|(if)|creator

		teacher:p|messages|messages_message|send
		student:c|messages|messages_message|delete|(if)|owner
		teacher:c|messages|messages_message|images-show
		teacher:c|messages|messages_message|images-add|(if)|creator
		teacher:c|messages|messages_message|images-remove|(if)|creator

		moderator:p|messages|messages_message|send
		moderator:c|messages|messages_message|images-show
		moderator:c|messages|messages_message|images-add|(if)|creator
		moderator:c|messages|messages_message|images-remove|(if)|creator
	</defaultpermissions>
</module>
*/ ?>
