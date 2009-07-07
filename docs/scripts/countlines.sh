#!/bin/bash

#
#    Count number of lines in directory
#        

for file in `find . -type f`; do cat $file; done | wc -l