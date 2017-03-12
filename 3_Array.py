#!/bin/python

# This script parse the input file.
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys
import csv

print ("Transformation script for the DATA table...")

infile = "input.csv"
outfile = "data_input.csv"

outdata = open(outfile, "w")

year_ID = 1960
UID = 0
rowindex = 1
with open(infile, 'rb') as csvfile:
	spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
	for row in spamreader:
		if (rowindex >= 6) and len(row) != 0:
			#print "row length: " + str(len(row)) + " Index " + str(rowindex)
			for y in range(3, 58): # For all the years
				#print "Data: " + row[y]
				if row[y].isdigit():	# Check if a cell is empty or not. If the length is more than 4 is not empty.
					UID += 1
					
					country_code = row[1]
					indic_code = row[3]
					dbRec = "\"" + str(UID) + "\"" + "," + "\"" +country_code + "\"" + "," + "\"" + indic_code + "\"" + "," + "\"" + str(year_ID) + "\"" + "," + "\"" + row[y] + "\"" + "\n"
					print dbRec
					outdata.write(dbRec)
				year_ID += 1
		rowindex += 1
		year_ID = 1960

outdata.close()

   