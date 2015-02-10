<html>
  <head>
    <title>video inventory</title>
  </head>
  <body>



<?php
include './credentials.php';

// output a form to add videos
echo <<<_END
<form method ='post' action='video_inventory.php'>
<table name='addBar'>
 <tr><td>name:</td> <td> <input type='text' name='name'></td>
     <td>category:</td> <td> <input type='text' name='category'></td>
     <td>length:</td> <td> <input type='text' name='length'></td>
     <td colspan='2' align='center'> <input type='submit' value='Add Video'></td></tr>
</table></form>
_END;

$mysqli = new mysqli($hostname, $username, $password, "290_assignment4_video_tracker");
if ($mysqli->connect_errno)
    {
        echo "Failed to connect to MySQL: (" . 
            $mysqli->connect_errno. ") " . 
            $mysqli->connect_error;
    }

echo "<table border=1><tr><th>id<th>name<th>category<th>length<th>rented</tr>";
$sql = "select id,name,category,length,rented from video_inventory";
$result = $mysqli->query($sql);
if ($result->num_rows>0)
    {
        // output data of each row
        while($row = $result->fetch_assoc())
            {
                echo "<tr><td> " . $row["id"]. "</td>".
                    "<td>" . $row["name"]. "</td>".
                    "<td>". $row["category"]. "</td>".
                    "<td> " . $row["length"]. "</td>".
                    "<td>" . $row["rented"]. "</tr>";
            }
    }
echo "</table>";


?>

</body>
</html>
