#!/bin/bash
# This script will set subversion properties for this project.
# Run this script from the trunk:
#  $ cd /path/to/library
#  $ etc/set_svn_properties.sh

svn propset svn:ignore -F etc/svn_ignores.txt .

