#!/bin/bash

cd "${BASH_SOURCE%/*}"

echo "Creating tar file."
tar -Jcf nak-web-0.1.tar.xz nak-web-0.1/

echo "Moving tar file to buildroot environment"
mv nak-web-0.1.tar.xz ../dl/

