echo "START BOOT SCRIPT"

# change our user to something else than UID 33
# this also proves we can use sudo
sudo sed -i 's/dockware:x:33:33:/dockware:x:7788:33:/g' /etc/passwd
