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
echo -e "\n\e[0m"
git pull
echo -e "\n"
echo -e "\e[0;32mMake PHP File Compile with Magic!\e[0m"
php magic compile

