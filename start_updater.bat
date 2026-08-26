@echo off
title Boleskine Gallery Updater
cd /d "%~dp0"
echo Starting Gallery Updater...
start "" http://localhost:5000
python updater.py
pause
