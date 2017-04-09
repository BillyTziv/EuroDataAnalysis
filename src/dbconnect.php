$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myDB";
// Create a c onnection with the database
$conn = mysqli_connect($servername, $username, $password, $dbname);
if( !$conn ) {
  die("Connection failed: " . mysqli_connect_error());
}
