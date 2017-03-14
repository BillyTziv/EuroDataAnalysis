#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys
import csv

print ("Transformation script started...")

# Input files
infile = "input.csv"
infile2 = "input2.csv"

# Output files
countries_out = "countries_out.csv"

#Create 3 lists
Countries = [list(), list(), list(), list()]

# Open all the necessary files to write data
countries_data = open(countries_out, "w")


# Get the countries and store them into an array
rowindex2 = 0
T = [list(), list()]
with open(infile, 'rb') as csvfile:
    spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
    c_index = 0
    for row in spamreader:
        if (rowindex2 >= 6) and len(row) != 0:
            country_name = row[0]
            country_code = row[1]
            if country_name not in T[0]:
                T[0].append(country_name)
                T[1].append(country_code)
                c_index += 1
        rowindex2 += 1
    print "Total country insertions: " + str(c_index)

rowindex = 1
rowLine2 = 0
with open(infile2, 'rb') as csvfile:
    spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
    for row in spamreader:
        if (rowindex2 >= 1) and len(row) != 0:
            if not row[1].isspace():
                country_code = row[0]
                country_region = row[1]
                inc_group = row[2]

                # Check if region is EUROPE
                if "Europe" in country_region:
                    #print "Europe country found..."
                    # Get country code and search for the name
                    code_index = T[1].index(country_code)
                    country_name = T[0][code_index]
                    strToWrite = "#" + country_code + "|" + country_region + "|" + inc_group + "|" + country_name + "\n"
                    countries_data.write(strToWrite)
        rowLine2 += 1


print "Transformation script ended..."
