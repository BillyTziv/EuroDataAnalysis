import sys
import os

if len(sys.argv) != 2:
    print "Usage: dates.py dates_output.txt\n\n"
    sys.exit()
    lc

infile = sys.argv[1]
myData = [list(), list(), list()]
with open(infile, "w") as f:
    for y in range(1960, 2014):
        myData[0].append(y)
        if y % 5 == 0:
            myData[1].append(y)
        if y % 10 == 0:
            myData[2].append(y)

    row=0
    fiveCounter=0
    tenCounter=0
    for y in myData[0]:
        if tenCounter < 6:
            texttofile = "#" + str(y) + "|" + str(myData[1][fiveCounter]) + "|" + str(myData[2][tenCounter]) + "\n"
            tenCounter += 1
        elif fiveCounter < 11:
            texttofile = "#" + str(y) + "|" + str(myData[1][fiveCounter]) + "\n"
            fiveCounter += 1
        else:
            texttofile = "#" + str(y) + "\n"
        f.write(texttofile)
f.close()
