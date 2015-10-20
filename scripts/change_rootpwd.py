#!/usr/bin/env python2

from getpass import *
from random  import *
from string  import *
from sys      import *
from passlib.hash import md5_crypt

if len(argv) > 1:
    password = argv[1]
else:
    password = getpass('Enter a new root password: ')
    passconf = getpass('Confirm password: ')
    if password != passconf:
        print "Password does not match."
        exit(-1)

# TODO: check password strength, warn about weak passwords.
if len(password) < 8:
    print 'Password must be at least 8 characters long.'
    exit(-1)

# Generate random salt and create (TODO: use sha512 with libpam) sha512 hash.
password_h = md5_crypt.encrypt(password) # NOT sha512

with open('files/etc/shadow', 'r') as f:
    lines = f.readlines()
    root_ent = [i for i, line in enumerate(lines) if 'root' in line]

    for i in root_ent:
        old_ent = lines[i].split(':')
        old_ent[1] = password_h
        old_ent[2] = '16464'
        old_ent[3] = '0'
        lines[i] = ':'.join(old_ent)

with open('files/etc/shadow', 'w') as f:
    print 'Updating shadow file (files/etc/shadow)...'
    f.writelines(lines)
