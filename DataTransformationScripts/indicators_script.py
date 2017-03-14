#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys
import csv

print ("Transformation script started...")

# Input files
infile = "input.csv"

# Output files
indicators_out = "indicators_output.csv"

# Open all the necessary files to write data
indicators_data = open(indicators_out, "w")

rowindex = 1
with open(infile, 'rb') as csvfile:
	spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
	for row in spamreader:
		if (rowindex >= 6) and len(row) != 0:
			# Get the indicator name and code and write to the correct outfile
			indic_name = row[2]
			indic_code = row[3]
			indicator_record = "#" + indic_name + "|" + indic_code + "\n"
			indicators_data.write(indicator_record)
			print indic_code + " # " + indic_name
		rowindex += 1

print "Closing the file..."
indicators_data.close()
