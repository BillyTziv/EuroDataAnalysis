#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys
import csv

print ("Transformation script started...")

# Input files
infile = "input.csv"

# Open all the necessary files to write data
info_data = open("info_out.csv", "w")

year_ID = 1960
rowindex = 1
with open(infile, 'rb') as csvfile:
	spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
	for row in spamreader:
		if (rowindex >= 6) and len(row) != 0:
			country_code = row[1]
			indic_code = row[3]
			for y in range(3, 58): # For all the years
				if row[y].isdigit():	# Check if a cell is empty or not. If the length is more than 4 is not empty.
					country_code = row[1]	# Country code found
					indic_code = row[3]		# Indicator code found

					info_record = "#" + country_code + "|" + indic_code + "|" + str(year_ID) + "|" + row[y] + "\n"
					info_data.write(info_record)					# Write a line in the file assosiated with the info table
				year_ID += 1
		rowindex += 1
		year_ID = 1960

print "Closing file..."
info_data.close()
