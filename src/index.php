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
      $SUBMIT_BUTTON = 0;
      $total_countries = 0;
      $myData = array();

    ?>

    <!-- CONNECT TO DATABASE -->
    <?php
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
    ?>

    <!-- INDICATOR SELECTED, DISPLAY THE COUNTRIES -->
    <?php
      // If an indicator is selected and countries have not loaded yet
      if( isset($_GET['indicator_list'])) {
        $sel_indicator = $_GET['indicator_list'];
        $COUNTRY_LOAD = 1;
        // Prepare a query that fetches all the counties according to the selected indicator
        $ind_query = "SELECT DISTINCT country_code FROM Info WHERE indicator_code = '" . $sel_indicator . "'";
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
      if( $conn && isset($_GET['country_list']) && isset($_GET['year_list']) && isset($_GET['pickind']) ) {
        $SUBMIT_BUTTON = 1;

        // Get the values from the form
        //$sel_country = $_GET['country_list'];
        $sel_year = $_GET['year_list'];
        $sel_chart = $_GET['chart_list'];
        $sel_indicator = $_GET['pickind'];
        $sqlindex = 0;
        $myres = array();
        $k=0;
        foreach ($_GET['country_list'] as $selectedOption) {
          $countries_selected[$k] = $selectedOption;
          $total_countries ++;
          // Prepare a query to fetch the data for the graph
          $myquery = "SELECT year_id, value FROM Info WHERE country_code = '" . $selectedOption . "' AND indicator_code = '" . $sel_indicator . "'" . "ORDER BY year_id";
          $myres[$sqlindex] = mysqli_query($conn, $myquery) or die("Database select error: ".mysqli_error($conn));
          //echo $myquery;
          $sqlindex += 1;
          $k += 1;
        }
        $num_of_countries = $sqlindex;

        /*while ($res = mysqli_fetch_array($myres[2], MYSQLI_ASSOC)) {
          echo $res['year_id'] . $res['value'] . "<br>";
        }*/

        // Create the first column with the years
        $year_number = 1960;
        for($t=0; $t<(2014-1960); $t++) {
          $myData[$t][0] = $year_number;
          $year_number++;
        }
        // For each column
        for($t=0; $t<$num_of_countries; $t++) {
          //echo $t . "st LIST " . "<br>";
          while ($res = mysqli_fetch_array($myres[$t], MYSQLI_ASSOC)) {
              $rec_year = $res['year_id'];
              $rec_value = $res['value'];
              $myData[$rec_year][$t] = $rec_value;
              echo "myData[" . $rec_year . "][" . $t . "] = " . $rec_value . "<br>";
              //echo $myData[$rec_year][$t];
          }
        }
      }

    ?>

    <!-- WRITE DATA TO FILE -->
    <?php
    //echo $myData[1962][1];
      //echo "The size is: " . sizeof($myData) . "!!!";
      //print_r($myData);
      for($i=0; $i<54; $i++) {
        if( isset($myData[$i][2]) ) {
          echo $myData[$i][2] . "\t";
        }
      }

      // If there is an active connection with the database
      if( $conn && isset($_GET['country_list']) && isset($_GET['year_list']) && isset($_GET['pickind']) ) {
        // Open a file and throw some data for the visualization
        $myfile = fopen("euroData.tsv", "w") or die("Unable to open file!");

        $c_type = $_GET['chart_list'];

        if ( $c_type == "linechart" ) {
          // Create the first line of the failed
          $txt = "date\t";
          for($k=0; $k<$num_of_countries; $k++) {
            $txt = $txt . $countries_selected[$k] . "\t";
          }
          fwrite($myfile, $txt."\n");
          for($row_index=0; $row_index<$total_rows; $row_index++) {
            $txt = $myData[$row_index][0] . "\t";
            for($country_ind=0; $country_ind<$total_countries; $country_ind++) {
              $txt = $txt . $myData[$row_index][$country_ind+1] . "\t";

            }
            fwrite($myfile, $txt."\n");
          }
        }else if ( $c_type == "barchart" ) {
          $txt = "letter\tfrequency\n";
          fwrite($myfile, $txt);

          for($i=0; $i<count($myData); $i++) {
            $txt = $myData[$i][0] . "\t" . $myData[$i][1] . "\n";
            fwrite($myfile, $txt);
          }
        }else if ( $c_type == "linechart" ) {

        }else {
          echo "Can' t write data to file, chart will not apear.\n";
        }
        fclose($myfile);
      }
    ?>
</head>
<body>
  <!--Include the header code -->
  <?php include('header.php'); ?>

  <div id="main">
    <table width="1200px">
      <tr>
        <td width="45%">
          <form action="index.php" method="get">
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
                      $myquery_c = "SELECT * FROM Indicators";
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
              <!--
              <div id="results_stats">
                <table>
                  <tr>
                    <th>Country</th>
                    <th>Indicator</th>
                    <th>Year mode</th>
                    <th>Chart type</th>
                  </tr>
                  <tr>
                    <td>
                      <?php
                        if( isset($sel_country)) {
                          //echo $sel_country;
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        if( isset($sel_indicator)) {
                          //echo $sel_indicator;
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        if( isset($sel_year)) {
                          //echo $sel_year;
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        if( isset($sel_chart)) {
                          //echo $sel_chart;
                        }
                      ?>
                    </td>
                  </tr>
                </table>



              </div>-->
              <div id="results_graph">
                <!-- Include the visualization code -->
                <?php
                  if( $sel_chart == "barchart") {
                    //echo "Bar chart is selected";
                    include('barchart.php');
                  }else if( $sel_chart == "scatterplot") {
                    //echo "Scatter splot is selected";
                    include('scatterplot.php');
                  }else if( $sel_chart == "linechart") {
                    //echo "Line chart is selected";
                    //include('linechart.php');
                  ?>
                    <!DOCTYPE html>
                    <meta charset="utf-8">
                    <style>

                    .axis--x path {
                      display: none;
                    }

                    .line {
                      fill: none;
                      stroke: steelblue;
                      stroke-width: 1.5px;
                    }

                    </style>
                    <svg width="650" height="400"></svg>
                    <script src="//d3js.org/d3.v4.min.js"></script>
                    <script>

                    var svg = d3.select("svg"),
                        margin = {top: 20, right: 80, bottom: 30, left: 50},
                        width = svg.attr("width") - margin.left - margin.right,
                        height = svg.attr("height") - margin.top - margin.bottom,
                        g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                    var parseTime = d3.timeParse("%Y");

                    var x = d3.scaleTime().range([0, width]),
                        y = d3.scaleLinear().range([height, 0]),
                        z = d3.scaleOrdinal(d3.schemeCategory10);

                    var line = d3.line()
                        .curve(d3.curveBasis)
                        .x(function(d) { return x(d.date); })
                        .y(function(d) { return y(d.temperature); });

                    d3.tsv("euroData.tsv", type, function(error, data) {
                      if (error) throw error;

                      var cities = data.columns.slice(1).map(function(id) {
                        return {
                          id: id,
                          values: data.map(function(d) {
                            return {date: d.date, temperature: d[id]};
                          })
                        };
                      });

                      x.domain(d3.extent(data, function(d) { return d.date; }));

                      y.domain([
                        d3.min(cities, function(c) { return d3.min(c.values, function(d) { return d.temperature; }); }),
                        d3.max(cities, function(c) { return d3.max(c.values, function(d) { return d.temperature; }); })
                      ]);

                      z.domain(cities.map(function(c) { return c.id; }));

                      g.append("g")
                          .attr("class", "axis axis--x")
                          .attr("transform", "translate(0," + height + ")")
                          .call(d3.axisBottom(x));

                      g.append("g")
                          .attr("class", "axis axis--y")
                          .call(d3.axisLeft(y))
                        .append("text")
                          .attr("transform", "rotate(-90)")
                          .attr("y", 6)
                          .attr("dy", "0.71em")
                          .attr("fill", "#000")
                          .text("Values");

                      var city = g.selectAll(".city")
                        .data(cities)
                        .enter().append("g")
                          .attr("class", "city");

                      city.append("path")
                          .attr("class", "line")
                          .attr("d", function(d) { return line(d.values); })
                          .style("stroke", function(d) { return z(d.id); });

                      city.append("text")
                          .datum(function(d) { return {id: d.id, value: d.values[d.values.length - 1]}; })
                          .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
                          .attr("x", 3)
                          .attr("dy", "0.35em")
                          .style("font", "10px sans-serif")
                          .text(function(d) { return d.id; });
                    });

                    function type(d, _, columns) {
                      d.date = parseTime(d.date);
                      for (var i = 1, n = columns.length, c; i < n; ++i) d[c = columns[i]] = +d[c];
                      return d;
                    }

                    </script>
                    <?php
                  }else {
                    //echo "An error occured. What did you choose? No available chart.";
                  }
                ?>
              </div>
          <?php  }
          ?>
        </td>
      </tr>
  </div> <!-- End of main -->
</body>
</html>
