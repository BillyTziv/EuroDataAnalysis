<html>
<head>
    <title>EuroDataAnalysis</title>

    <link rel="stylesheet" type="text/css" href="theme.css">
    <link href="https://fonts.googleapis.com/css?family=Slabo+27px" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="mytheme.css">-->

    <?php
      // Initialization of varialbes
      $EMPTYQUERY = 0;
      $sel_chart = "";
      $COUNTRY_LOAD = 0;
      $msg_eror = "";
      $SUBMIT_BUTTON = 0;
      $total_countries = 0;
      $myData = array();
      
      // Database settings
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "myDB";

      // Trying to connect to the database
      $conn = mysqli_connect($servername, $username, $password, $dbname);
      if( !$conn ) {
        die("Database connection failed: " . mysqli_connect_error());
      }
    

      // If an indicator is selected and countries have not loaded yet
      if( isset($_POST['indicator_list'])) {
        $sel_indicator = $_POST['indicator_list'];
        $COUNTRY_LOAD = 1;


        // Prepare a query that fetches all the counties according to the selected indicator
        $ind_query = "SELECT DISTINCT country_code FROM Info WHERE indicator_code = '" . $sel_indicator . "' AND value <> '0'";
        $count_query = "SELECT * FROM Countries WHERE country_code IN (" . $ind_query . ")";
        $coun_res = mysqli_query($conn, $count_query) or die("Database select error: ".mysqli_error($conn));

        /*while ($r2 = mysqli_fetch_array($coun_res, MYSQLI_ASSOC)) {
            echo $r2['country_name'] . "<br>";
        }*/
      }
    ?>

    <!-- VISUALIZATION DATA -->
    <?php
      // If a country is selected
      if( $conn && isset($_POST['country_list']) && isset($_POST['year_list']) && isset($_POST['pickind']) ) {
        $SUBMIT_BUTTON = 1;

        // Open a new file to store all the data
        $data = fopen("euroData.tsv", "w") or die("Unable to open data file!");

        // Store data into cookies
        $_SESSION['post-data'] = $_POST;
        

        // Store data into varialbes for the rest of the code
        $sel_year = $_POST['year_list'];
        $sel_chart = $_POST['chart_list'];
        $sel_indicator = $_POST['pickind'];
        $sel_countries = $_POST['country_list'];

        //echo "Selected year: " . $sel_year;echo "</br>";
        //echo "Selected chart type: " . $sel_chart;echo "</br>";
        //echo "Selected indicator: " . $sel_indicator;echo "</br>";
        //echo "Selected countries: " . $sel_countries[0];echo "</br>";
        //echo "</br> </br>";

        //echo "Indicator is: " . $_SESSION['post-data']['pickind'] . "</br>";

        $countries_total_number = count($sel_countries);
        $sqlindex = 0;
        $myres = array();
        $D = array();
        $k=0;

        // File number for each selected country
        $file_index = 0;

        // Depending on the chart type write the correct header
        if( $sel_chart == "linechart" ) {
          $header = "date\t";
          foreach ( $sel_countries as $c) {
            $header = $header . $c . "\t";
          }
          fwrite($data, $header . "\n");
        }else if( $sel_chart == "barchart" ) {
          if( $countries_total_number == 1 ) {
            $header = "letter\tfrequency";
          }
          fwrite($data, $header . "\n");
        }else if ( $sel_chart == "scatterplot" ) {
          $header = "sepalLength\tsepalWidth\tpetalLength\tpetalWidth\tspecies";
          fwrite($data, $header . "\n");
        }else {
          $msg_error = "No chart type was selected!";
          exit;
        }

        // Prepare a query to fetch the data for the graph
        foreach ( $sel_countries as $c) {
          // Create a query
          if( $sel_year == "single_year" ) {
            $myquery = "SELECT year_id, value FROM Info WHERE country_code = '" . $c . "' AND indicator_code = '" . $sel_indicator . "'" . "ORDER BY year_id";
            //echo $myquery
          }elseif( $sel_year == "five_year" ) {
            $myquery = "SELECT year_id, AVG(value) FROM Dates INNER JOIN Info on Dates.date_id = Info.year_id WHERE country_code = '" . $c . "' AND indicator_code = '" . $sel_indicator . "'" . " ORDER BY year_id GROUP BY fiveYrPer";
            //echo $myquery;
          }elseif( $sel_year == "ten_year" ) {
            $myquery = "SELECT year_id, AVG(value) FROM Dates INNER JOIN Info on Dates.date_id = Info.year_id WHERE country_code = '" . $c . "' AND indicator_code = '" . $sel_indicator . "'" . " ORDER BY year_id GROUP BY decade";
            //echo $myquery;
          }else {
            $msg_error = "Oups there was a problem with the SQL query!";
            exit;
          }

          // Get the results from the database
          $myres = mysqli_query($conn, $myquery) or die("Database query error: " . mysqli_error($conn));

          // Open a new file to write data and increase the file index 
          $file_name = "euroData" . $file_index . ".tsv";
          $myfile = fopen($file_name, "w") or die("Unable to open file!");
          $file_index += 1;

          // Create files with the information of the sql query. Every file
          // will contain information about a country
          while($row = mysqli_fetch_array($myres)) {
            //echo "Year ID: " . $row['year_id'] . "\tValue: " . $row['value'] . "</br>";
            $txt = $row['year_id'] . "\t" . $row['value'];
            fwrite($myfile, $txt."\n");
          }
          fclose($myfile);
        } // END foreach country
      
        
        // Create the first column with the years
        $year_number = 1960;
        for($t=0; $t<=(2014-1960); $t++) {
          //echo "Inserting to [0][" . $t . "] : " . $year_number . "</br>";
          $D[$t][0] = $year_number;
          for($l=1; $l<$countries_total_number; $l++) {
            //echo "Inserting to [" . $l . "][" . $t . "] : " . 0 . "</br>";
            $D[$t][$l] = 0;
          }
          $year_number++;
        }
        
        // Read the files and create one file with all the date
        $file_index = 0;
        for ( $country_index=1; $country_index<= $countries_total_number; $country_index++) {
          $file_name = "euroData" . $file_index . ".tsv";
          $handle = fopen($file_name, "r");

          if ($handle) {
            while (($line = fgets($handle)) !== false) {
              $parts = preg_split('/\s+/', $line);
              //print_r( $parts );
              $c_year = $parts[0];
              $c_value  = $parts[1];

              // Search for the keyword
              for($fkey=1; $fkey<54; $fkey++) {
                if($D[$fkey][0] == $c_year) {
                  //echo "Index found at: " . $fkey . "</br>";
                  $D[$fkey][$country_index] = $c_value;
                }
              }
            }
            fclose($handle);
            $file_index += 1;
          }else {
            $msg_error = "Error while opening/reading the file: " . $file_name;
            exit;
          } 
        }

        //echo "</br>". "Selected countries: " . count($sel_countries). "</br>";

        if( $sel_chart == "linechart" ) {
          $txt = "";
          for($i=1; $i<54; $i++) {
            for($j=0; $j<=$countries_total_number; $j++) {
                
                //echo "D[". $j . "]" . "[". $i . "]" . ": " . @$D[$i][$j] . "</br>";
                if( @$D[$i][$j] == "") {
                  $txt = $txt . "0" . "\t";
                }else {
                  $txt = $txt . $D[$i][$j] . "\t";
                }

            }
            $txt = $txt . "\n";
            fwrite($data, $txt);
            $txt = "";
          }
        }else if( $sel_chart == "barchart" ) {
          $txt = "";
          if($countries_total_number == 1) {
            echo "Selected Bar chart...\n";
            for($i=1; $i<54; $i++) {
              if( @$D[$i][1] == "") {
                //$txt = $txt . $D[$i][0] . "\t" . "0";
                // Do nothing
              }else {
                $txt = $txt . $D[$i][0] . "\t" . $D[$i][1] . "\n";
                fwrite($data, $txt);
                $txt = "";
              }
            }
          }
        }else if ( $sel_chart == "scatterplot" ) {
          echo "Creating the scatter plot data...";
          $txt = "";
          for($j=0; $j<$countries_total_number; $j++) {
                for($i=1; $i<54; $i++) {
		        //echo "D[". $j . "]" . "[". $i . "]" . ": " . @$D[$i][$j] . "</br>";
                        if( @$D[$i][$j] != "" && @$D[$i][1] != 0 ) {
                                //$txt = $D[$i][0] . "\t" . $D[$i][$j+1] . "\t1\t1\t" . $sel_countries[$j];
                                $txt = $D[$i][0] . "\t". $D[$i][1] . "\t" . "1\t1\t" . $sel_countries[$j];
                                fwrite($data, $txt . "\n");
                        }
                $txt = "";
                }
                
          }
        }else {
          $msg_error = "No chart type was selected!";
          exit;
        }
      }


    ?>
</head>
  <body>
    <div id="msg_bar">
      <?php echo $msg_eror; ?>
    </div>
    
    <div id="header_section">
      <div id="header">
        <div id="header_title"> EuroDataAnalysis </div>
        <div id="header_subtitle">"MYE[030] Advanced Topics of Database Technology and Applications"</div>
      </div>
    </div>

    <!-- Everything down from the header -->
    <div id="main">
      <table width="1200px">
        <tr>
          <td width="45%">
            <form action="index.php" method="post">
            
              <!-- Smart button -->
              <input type="hidden" value="<?php if( isset($sel_indicator)) { echo $sel_indicator; }?>" name="pickind" />

              <div class="main_header">
                      STEP 1 - Select an indicator
              </div>
              
              <div class="main_box">
                <table width="500px">
                  <tr>
                    <td>
                      <!-- Dropdown menu with the indicators -->
                      <?php
                        echo "<select size=10 name=\"indicator_list\">";
                        $temp = "SELECT indicator_code FROM Info WHERE value <> 0";

                        $myquery_c = "SELECT * FROM Indicators WHERE ind_code IN (" . $temp .")";

                        $result_c = mysqli_query($conn, $myquery_c);

                        while($row_c = mysqli_fetch_array($result_c, MYSQLI_ASSOC)) {
                                //$x = $row_c['ind_code'];
                                $indicator_name_list_item = $row_c['ind_name'];
                                $indicator_code_list_item = $row_c['ind_code'];
                                echo "<option value=\"" . $indicator_code_list_item . "\">" . $indicator_name_list_item . "</option>";
                        }
                        echo "</select>"
                      ?>
                    </td>
                  </tr>
                          
                  <tr>
                    <td>
                      <div id="step1_button">
                              <input id="find_countries" type="submit" value="Find countries">
                      </div>
                      <?php
                      if( $COUNTRY_LOAD == 0 ) {
                              echo "";
                      }else {
                              echo "<div id=\"step1_verification\">Countries updated!</div>";
                      }
                      ?>
                    </td>
                  </tr>
                </table>
              </div>

              <div class="main_header">
                      STEP 2 - Select a country and the year mode
              </div>

              <div id="step2_box" class="main_box">
                <table width="400px">
                  <tr>
                    <td>
                      <!-- Dropdown menu with the coutnires -->
                      <?php
                        echo "<select multiple size=5 width=20 name=\"country_list[]\">";
                        if( $COUNTRY_LOAD == 1 ) {
                          while($row_c = mysqli_fetch_array($coun_res, MYSQLI_ASSOC)) {
                              $country_name_list_item = $row_c['country_name'];
                              $country_code_list_item = $row_c['country_code'];
                              echo "<option value=\"" . $country_code_list_item . "\">" . $country_name_list_item . "</option>";
                          }
                        }else {
                          $myquery_c = "SELECT * FROM Countries";
                          $c_res = mysqli_query($conn, $myquery_c);
                          while($row_c = mysqli_fetch_array($c_res, MYSQLI_ASSOC)) {
                              $country_name_list_item = $row_c['country_name'];
                              $country_code_list_item = $row_c['country_code'];
                              echo "<option value=\"" . $country_code_list_item . "\">" . $country_name_list_item . "</option>";
                          }
                        }
                        echo "</select>"
                      ?>
                    </td>
                    
                    <td>
                      <select name="year_list" size=5>
                        <option value="single_year">Single year mode</option>
                        <option value="five_year">Five year mode</option>
                        <option value="ten_year">Ten year mode</option>
                      </select>
                    </td>
                  </tr>
                  
                  <tr>
                    <td> * keep Ctrl key for multiple selection</td>
                  </tr>
                </table>
              </div>

              <div class="main_header">
                STEP 3 - Pick your chart type
              </div>
          
              <div class="main_box">
                <table>
                  <tr>
                    <td>
                      <select align="left" name="chart_list" size=3>
                        <option value="linechart">Line chart</option>
                        <option value="barchart">Bar Chart</option>
                        <option value="scatterplot">Scatter Plot</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div id="submit_button">
                        <input id="visualize_button" type="submit" value="Create chart">
                      </div>
                      <?php
                        if( $SUBMIT_BUTTON == 0 ) {
                          echo "";
                        }else {
                          echo "<div id=\"step3_verification\">Complete!</div>";
                        }
                      ?>
                    </td>
                  </tr>
                </table>
              </div>
            </form>
          </td>

          <td width="55%">
            <?php
              if( $SUBMIT_BUTTON == 1 ) {
            ?>
                <div id="results_title">
                  * Results *
                </div>

                  <!-- Include all the charts or plots depending on the selected type -->
                <div id="results_graph">
                  <?php
                  if( $sel_chart == "barchart") {
                          include("charts/barchart.php");
                          $msg_error = "No chart type was selected!";
                  }else if( $sel_chart == "scatterplot") {
                          include("charts/scatterplot.php");
                  }else if( $sel_chart == "linechart") {
                          include("charts/linechart.php");
                  }else {
                    echo "Priting hterirs";
                    $msg_error = "No chart type was selected!";
                  }
                  ?>
                </div>
            <?php  }
            ?>
          </td>
        </tr>
      </table>
    </div> <!-- End of main -->
  </body>
</html>