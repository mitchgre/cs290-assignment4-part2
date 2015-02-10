<html>
  <head>
    <title>video inventory</title>
  </head>
  <body>



<?php

checkPostRequests();
addVideoForm();
$mysqli = connectToDB();
drawVideos($mysqli);


// handle post requests via mysql
function checkPostRequests(){
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if (! empty($_POST))
            {
                if ( isset($_POST["name"]) && $_POST["name"] != null && $_POST["name"] != '')
                    {
                        if ( isset($_POST["category"]) && $_POST["category"] != null)
                            {
                                if ( isset($_POST["length"]) && $_POST["length"] != null)
                                    {
                                        // everything is set here. 
                                        // just need to tell mysql 
                                        $name = $_POST["name"];
                                        $category = $_POST["category"];
                                        $length = $_POST["length"];
                                        
                                    }
                                else // length is NULL.  This is okay.
                                    {
                                        $name = $_POST["name"];
                                        $category = $_POST["category"];
                                        $length = "";
                                    }
                            }
                        else // category is NULL.  This is okay.
                            {
                                if ( isset($_POST["length"]) && $_POST["length"] != null)
                                    { 
                                        $name = $_POST["name"];
                                        $category = "";
                                        $length = $_POST["length"];
                                    }
                                else // length is NULL.  This is okay.
                                    {
                                        $name = $_POST["name"];
                                        $category = "";
                                        $length = "";

                                    }
                            }

                        $mysqli = connectToDB();
                        $sql = "insert into video_inventory ".
                            "(name,category,length) ".
                            "values (\"$name\",\"$category\",\"$length\");";
                        // echo "Trying to add $sql";
                        $result = $mysqli->query($sql);
                    }
                else // name is NULL.  This is NOT okay.
                    {
                        echo "You must have a name.";
                    }
            } // end check for empty post
    } // end check if server request is post type 
}


// output a form to add videos
function addVideoForm() {
    echo <<<_END
<form method ='post' action='video_inventory.php&add=true'>
<table name='addBar'>
 <tr><td>name:</td> <td> <input type='text' name='name'></td>
     <td>category:</td> <td> <input type='text' name='category'></td>
     <td>length:</td> <td> <input type='text' name='length'></td>
     <td colspan='2' align='center'> <input type='submit' value='Add Video'></td></tr>
</table></form>
_END;
}


// output a form to delete videos
function delVideoForm() {
    echo <<<_END
<form method ='post' action='video_inventory.php&delete=true'>
<input type='submit' value='Delete'>
</form>
_END;
}


function connectToDB(){
include './credentials.php';  // username, hostname,password
$mysqli = new mysqli($hostname, $username, $password, "290_assignment4_video_tracker");
if ($mysqli->connect_errno)
    {
        echo "Failed to connect to MySQL: (" . 
            $mysqli->connect_errno. ") " . 
            $mysqli->connect_error;
    }
return $mysqli;
}

function drawVideos($mysqli){
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
                    "<td> " . $row["length"]. "</td>";
                    // "<td>" . $row["rented"]. "</tr>";
                    if ($row["rented"] == 0)
                        echo "<td>available</td>";
                    else 
                        echo "<td>checked out</td>";
                    echo "<td>";
                    delVideoForm(); 
                    echo "</td></tr>";
            }
    }
echo "</table>";
}

?>

</body>
</html>
