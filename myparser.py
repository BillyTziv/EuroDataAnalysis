#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys

print ("Welcome to Mr. Parser...")

# Get the First input file  "input"
infile = "input.csv"
print ("Parsing input file: " + infile)

# Ge
# Open the input file
data = open(infile, "r")
content= data.readlines()

#Create 3 lists
Indicator = [list(), list()]

#Read input1
rowLine = 0
lineBuf = ""
for con in content:
    if rowLine >= 5:
        # I found a new country
        lineBuf = con.replace('"', "").split(",")
        country_name = lineBuf[0]
        country_code = lineBuf[1]

        if country_name not in Countries[0]:
            print "Inserting a new record: [" + country_name + ", " + country_code + "]"
            rowLine2 = rowLine2 + 1
    rowLine = rowLine + 1

listSize = len(Indicator[3])
for index in range(0, listSize):
    print (Indicator[0][index] + "," + Indicator[1][index]
