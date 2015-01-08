#!/bin/bash

cd "${BASH_SOURCE%/*}"

echo "Creating tar file."
tar -Jcf sbox-web-0.1.tar.xz sbox-web-0.1/

echo "Moving tar file to buildroot environment"
mv sbox-web-0.1.tar.xz ../dl/

