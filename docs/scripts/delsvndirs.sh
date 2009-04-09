#!/bin/bash

#
# Delete all .svn directories in curent dirrectory (recursive)
#

find -type d -name .svn -exec rm {} -R -f \;


