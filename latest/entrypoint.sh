#!/bin/bash

echo ""
echo " _____   ____   _____ _  ____          __     _____  ______ "
echo "|  __ \ / __ \ / ____| |/ /\ \        / /\   |  __ \|  ____|"
echo "| |  | | |  | | |    | ' /  \ \  /\  / /  \  | |__) | |__   "
echo "| |  | | |  | | |    |  <    \ \/  \/ / /\ \ |  _  /|  __|  "
echo "| |__| | |__| | |____| . \    \  /\  / ____ \| | \ \| |____ "
echo "|_____/ \____/ \_____|_|\_\    \/  \/_/    \_\_|  \_\______|"
echo ""
echo "68 69 20 64 65 76 65 6C 6F 70 65 72 2C 20 6E 69 63 65 20 74 6F 20 6D 65 65 74 20 79 6F 75"
echo "6c 6f 6f 6b 69 6e 67 20 66 6f 72 20 61 20 6a 6f 62 3f 20 77 72 69 74 65 20 75 73 20 61 74 20 6a 6f 62 73 40 64 61 73 69 73 74 77 65 62 2e 64 65"
echo ""
echo "*******************************************************"
echo "** DOCKWARE IMAGE: dev"
echo "** Tag: latest"
echo "** Version: 1.7.0"
echo "** Built: $(cat /build-date.txt)"
echo "** Copyright 2022 dasistweb GmbH"
echo "*******************************************************"
echo ""
echo "launching dockware...please wait..."
echo ""

set -e

#source /etc/apache2/envvars
#source /var/www/.bashrc

# this is important to automatically use the bashrc file
# in the "exec" command below when using a simple docker runner command
export BASH_ENV=/var/www/.bashrc

CONTAINER_STARTUP_DIR=$(pwd)


# always execute custom commands in here.
# if a custom command is provided, then the container
# will automatically exit after it.
# that's somehow just how it works.
# otherwise it will continue with the code below
exec "$@"

# we still need this to allow custom events
# such as our BUILD_PLUGIN feature to exit the container
if [[ ! -z "$DOCKWARE_CI" ]]; then
    # CONTAINER WAS STARTED IN NON-BLOCKING CI MODE...."
    # DOCKWARE WILL NOW EXIT THE CONTAINER"
    echo ""
else
    tail -f /dev/null
fi

