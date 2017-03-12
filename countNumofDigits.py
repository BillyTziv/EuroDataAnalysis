#!/bin/python

# This script calculates the bumber of numbers
# Tzivaras Vasilis 1770, Tzivaras Panagiotis 1931
# vtzivaras@gmail.com, ptzivaras@gmail.com

import sys
import csv

print ("Calculate the number of numbers in a csv file")

infile = "input.csv"

numofDigits = 0
with open(infile, 'rb') as csvfile:
	spamreader = csv.reader(csvfile, delimiter=',', quotechar='"')
	for row in spamreader:
		for col in row:
			if col.isdigit():
				numofDigits += 1
print "\nTotal number of digits: " + str(numofDigits)
print "\n" 