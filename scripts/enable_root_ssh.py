#!/usr/bin/env python2

with open('.config', 'a') as f:
    f.write('CONFIG_PACKAGE_openssh-client=y\n' + \
            'CONFIG_PACKAGE_openssh-client-utils=y\n' + \
            'CONFIG_PACKAGE_openssh-keygen=y\n' + \
            'CONFIG_PACKAGE_openssh-sftp-client=y\n' + \
            'CONFIG_PACKAGE_openssh-sftp-server=y\n' + \
            'CONFIG_PACKAGE_openssh-server=y\n' + \
            'CONFIG_PACKAGE_openssh-server-pam=y\n')

with open('files/etc/passwd', 'r') as f:
    lines = f.readlines()
    root_ent = [i for i, line in enumerate(lines) if 'root' in line]

    for i in root_ent:
        ent = lines[i].split(':')
        ent[6] = '/bin/ash\n'
        lines[i] = ':'.join(ent)

with open('files/etc/passwd', 'w') as f:
    print 'Updating passwd file (files/etc/passwd)...'
    f.writelines(lines)
