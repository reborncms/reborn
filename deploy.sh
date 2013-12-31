#!/bin/bash
clear
echo -e "\033[36m - - - Loading - - -"
cat <<EOF
* =================================================== *
* Reborn CMS Deployment Shell for Reborn Developer    *
* You can make easy git pull from origin and          *
* Compile with Magic Tool.                            *
* =================================================== *
EOF
echo -e "\n"
git pull
echo -e "\n"
echo "Make PHP File Compile with Magic!"
php magic compile

