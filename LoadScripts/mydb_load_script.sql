# This script loads data into the db tables

# LOAD DATA INFILE '/home/mark/Desktop/EuroDataVisualization/indicators_output.csv' INTO TABLE Indicators FIELDS TERMINATED BY '|'  LINES STARTING BY '#' (ind_code, ind_name) SET id=NULL;
#LOAD DATA INFILE '/home/mark/Desktop/EuroDataVisualization/countries_out.csv' INTO TABLE Countries FIELDS TERMINATED BY '|'  LINES STARTING BY '#' (country_name, country_code, region, inc_group) SET id=NULL;
LOAD DATA INFILE '/home/mark/Desktop/EuroDataVisualization/info_out.csv' INTO TABLE Info FIELDS TERMINATED BY '|'  LINES STARTING BY '#' (country_code, indicator_code, year_id, value) SET id=NULL;
