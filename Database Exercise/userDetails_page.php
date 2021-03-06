<?php 
    // connect to db
    $connect = mysqli_connect("127.0.0.1", "root", "", "Qgiv");
    // Check connection
    if ($connect->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<?php
//function for translating the date
function dob_config($dobstr){
    $newstr = substr($dobstr, 0, -10);
    $numbers = explode('-', $newstr);
    //switch statement for month handling
    switch ($numbers[1]) {
    case "01":
        $dobMonth = "January";
        break;
    case "02":
        $dobMonth = "February";
        break;
    case "03":
        $dobMonth = "March";
        break;
    case "04":
        $dobMonth = "April";
        break;
    case "05":
        $dobMonth = "May";
        break;
    case "06":
        $dobMonth = "June";
        break;
    case "07":
        $dobMonth = "July";
        break;
    case "08":
        $dobMonth = "August";
        break;
    case "09":
        $dobMonth = "September";
        break;
    case "10":
        $dobMonth = "October";
        break;
    case "11":
        $dobMonth = "November";
        break;
    case "12":
        $dobMonth = "December";
        break;
}
    $newstr = "{$dobMonth} {$numbers[1]}, {$numbers[0]}";
    return $newstr;
}
?>
<!--------------------------------------------------------------------------------------------------------------------------------------------------->
<!DOCTYPE html>
<html>
<head>
<style>
ul#input li {
  display:inline;
}
</style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width" />

    <link rel="stylesheet" type="text/css" href="https://secure.qgiv.com/resources/admin/css/application.css" />

    <style type="text/css">
        .container{ max-width: 1200px; margin: 0 auto; }
        .logo-header{ padding: 2em; }
        .logo{ margin: 0 auto; min-height: 80px; }
    </style>
</head>
<!----------------------------------------------------------------------------------------------------------------------------------------------------->

<body class="container-fluid allApps_page">
<div class="logo-header">
    <img class="logo" src="https://secure.qgiv.com/resources/core/images/logo-qgiv.svg" alt="Qgiv logo" />
</div>

<li class="nav justify-content-center">
    <button onclick="location.href='index.php'" type="button">Users</button>
    <button onclick="location.href='transactions.php'" type="button">Transactions</button>
</li>

<form action="scripts/updateApp.php?applicant_id=<?php echo $_GET["results_email"]?>" method="POST" id="appForm" enctype="multipart/form-data">  
    <div class="row">
        <div class="col-sm-8">
            <h2>Personal Information</h2>
        </div>
    </div>
</form>
<!------------------------------------------------------------------------------------------------------------------------------------------------------>

<table id = "myTable" class="data-table">
    <?php
    $results_email = $_GET["results_email"];

//query for user table---------------------------------------------------------------------------------------------------------------------------------->

    $msql = "SELECT CONCAT(results_name_title, ' ', results_name_first, ' ', results_name_last) as Name, CONCAT(results_location_street, ' ', results_location_city, ' ', results_location_state, ' ', results_location_postcode) as Address, CONCAT(results_location_timezone_description, ' ', results_location_timezone_offset) as timezone, results_phone, results_cell, results_dob_date, results_location_coordinates_longitude, results_location_coordinates_latitude, results_picture_large, results_registered_date, results_registered_age FROM `users` WHERE results_email='". $results_email. "'";

    $result = $connect->query($msql);
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
             $image = $row["results_picture_large"];
             $imageData = base64_encode(file_get_contents($image));
             echo '<b>Name: </b>' . ucwords($row["Name"]) . '<br>';
             echo '<b>Date of Birth: </b>' . dob_config($row["results_dob_date"]) . '<br>';
             echo '<b>Address: </b>' . $row["Address"] . '<br>';
             echo '<b>Email: </b>' . $results_email . '<br>';
             echo '<b>Home Phone: </b>' . $row["results_phone"] . '<br>';
             echo '<b>Cell Phone: </b>' . $row["results_cell"] . '<br>';
             echo '<b>Latitude: </b>' . $row["results_location_coordinates_latitude"] . '<br>';
             echo '<b>Longitude: </b>' . $row["results_location_coordinates_longitude"] . '<br>';
             echo '<b>Timezone: </b>' . $row["timezone"] . '<br>';
             echo '<b>Registration Date: </b>' . $row["results_registered_date"] . '<br>';     
             echo '<b>Age of Registration: </b>' . $row["results_registered_age"] . '<br>';        
             echo '<img src="data:image/jpeg;base64,'.$imageData.'">';
        }
    }
    ?>
</table>
<!------------------------------------------------------------------------------------------------------------------------------------------------------->
<h1>New Transaction</h1>
<!---Form for new transaction--->
<form action="insert.php" method="post">
    <ul id="input">
    <li>Amount: <input type="text" name="amount" /></li>
    <li>Status: <input type="text" name="status" /></li>
    <li>Payment Type: <input type="text" name="payment_type" /></li>
    <input type='hidden' name='user_email' value='<?php echo "$results_email";?>'/> 
    <input type="submit" />
    </ul>
</form>

        <div class="data-table-container">
            <table id = "myTable" class="data-table">
                <thead>
                    <tr>
                        <!---- Table sorting header ----->
                        <th onclick ="sortTable(0)" class="ui-secondary-color">ID</th>
                        <th onclick ="sortTable(1)" class="ui-secondary-color">Timestamp</th>
                        <th onclick ="sortTable(2)" class="ui-secondary-color">Amount</th>
                        <th onclick ="sortTable(3)" class="ui-secondary-color">Status</th>
                        <th onclick ="sortTable(4)" class="ui-secondary-color">Payment Type</th>
                        <th onclick ="sortTable(5)" class="ui-secondary-color">User Email</th>
                    </tr>
                </thead>
                <tbody>
                
                <?php

                if (isset($_GET['pageno'])) {
                $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }
                $no_of_records_per_page = 20;
                $offset = ($pageno-1) * $no_of_records_per_page;

                $total_pages_sql = "SELECT COUNT(*) FROM transactions";
                $result = mysqli_query($connect,$total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $sql = "SELECT * FROM transactions LIMIT $offset, $no_of_records_per_page";
                $res_data = mysqli_query($connect,$sql);

                //query for transaction table--------------------------------------------------------------------------------------------------->
                $results_email = $_GET["results_email"];
		        $msql = "SELECT idtransactions, amount, status, payment_type, user_email, timestamp FROM `transactions` WHERE user_email='". $results_email. "'";
                
                $result = $connect->query($msql);
                if($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()){
                        echo "<tr>
                        <td>". $row["idtransactions"] . "</td>
                        <td>". $row["timestamp"] . "</td>
                        <td>". $row["amount"] . "</td>
                        <td>". $row["status"]. "</td>
                        <td>". $row["payment_type"]. "</td>
                        <td>". $row["user_email"]. "</td>
                        </tr>";
    	            }
                }

    $connect->close();
	?>

                </tbody>
            </table>
        </div>
    </section>

<script>
/*   Table Sorting Script    */
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("myTable");
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;      
    } else {
          if (switchcount == 0 && dir == "asc") {
              dir = "desc";
              switching = true;
          }
      }
  }
}
</script>

</body>
</html>
