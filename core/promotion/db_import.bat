@echo off
:: Remove Existing databases
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot -e "DROP DATABASE IF EXISTS bmv
Pause

:: Create new databases
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot -e "CREATE DATABASE `bmv` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci"
Pause

:: Import sql file
c:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe -u root -proot bmv < .\backup_22_oct.sql
Pause
