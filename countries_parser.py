#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys

print ("Welcome to Mr. Parser...")

# Get the First input file  "input"
infile = "input.csv"
print ("Parsing input file: " + infile)

infile2 = "input2.csv"
print ("Parsing the input file" + infile2)
# Ge
# Open the input file
data = open(infile, "r")
data2 = open(infile2, "r")
content= data.readlines()
content2 = data2.readlines()

#Create 3 lists
Countries = [list(), list(), list(), list()]

#Read input1
rowLine = 0
rowLine2 = 0
lineBuf = ""
lineBuf2 = ""
for con2 in content2:
    if rowLine2 >= 1:
        # I found a new country
        lineBuf2 = con2.replace('"', "").split(",")
        #print "=========\n" + str(lineBuf2)
        if not con2.isspace():
            country_region = lineBuf2[1]

            # Check if region is EUROPE
            if "Europe" in country_region:
                #print "Europe country found..."
                # Get country code and search for the name
                country_code = lineBuf2[0]
                inc_group = lineBuf2[2]
                for con in content:
                    if rowLine >= 5:
                        # I found a new country
                        lineBuf = con.replace('"', "").split(",")
                        search_token = lineBuf[1]
                        if country_code == search_token:
                            #print "\tFound country details"
                            # We found the information needed
                            country_name = lineBuf[0]
                            Countries[0].append(country_name)
                            Countries[1].append(country_code)
                            Countries[2].append(country_region)
                            Countries[3].append(inc_group)
                            break
                    rowLine += 1
                rowLine = 0
                lineBug = ""
    rowLine2 += 1

listSize = len(Countries[3])
for index in range(0, listSize):
    print (Countries[0][index] + "," + Countries[1][index] + "," + Countries[2][index]) + "," + Countries[3][index]
