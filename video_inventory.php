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


function preparedStatement($query){
    $mysqli = connectToDB();
    if( $sql = $mysqli->prepare($query)){
        $sql->execute();
        return true;
    }
    return false;
}

// delete a row from the table with the given id
// the alternate approach is to use hidden inputs, which might be more robust
function checkDeletions(){
    $mysqli = connectToDB();

    if ( isset($_POST["DeleteAll"]) && $_POST["DeleteAll"] != null){
        // stage1: prepare
        $query = "Truncate video_inventory ";
        
        // stage2: execute (no need to bind)
        if( $sql = $mysqli->prepare($query)){
            $sql->execute();
            return true;
        }
    }

    $id = array_search('Delete',$_POST); // search post array for the id

    $query = "delete from video_inventory ".
        "where id = ".$id;
    
    if (preparedStatement($query)){
        // reset counter on primary key
        $query = "alter table video_inventory drop id";
        if (preparedStatement($query)){
            $query = "alter table video_inventory auto_increment = 1";
            if (preparedStatement($query)){
                $query = "alter table video_inventory add id".
                    " int unsigned not null auto_increment primary key first";
                if (preparedStatement($query)){
                    return true;
                }
            }
        }
    }


    return false;
}


function checkInsertions(){
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
    /*
    else // name is NULL.  This is NOT okay.
        {
            echo "You must have a name.";
        }
    */
    
}


// handle post requests via mysql
function checkPostRequests(){
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if (! empty($_POST))
            {
                if(!checkDeletions()) // if we're not deleting
                    checkInsertions();
            } // 
    } // 
} // end 



// output a form to add videos
function addVideoForm() {
    echo <<<_END
<form method ='post' action='video_inventory.php'>
<table name='addBar'>
 <tr><td>name:</td> <td> <input type='text' name='name'></td>
     <td>category:</td> <td> <input type='text' name='category'></td>
     <td>length:</td> <td> <input type='text' name='length'></td>
     <td colspan='2' align='center'> <input type='submit' value='Add Video'></td></tr>
</table></form>
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
    echo "<form action='' method='post'>";
    echo "<table border=1><tr><th>id<th>name<th>category<th>length<th>rented";
    echo '<th><input type="submit" name="DeleteAll" value="Delete All Videos"></tr>';
    
// prepare statement
    $query = "select id,name,category,length,rented from video_inventory";
    if ($sql=$mysqli->prepare($query)){
        
        $sql->execute();
    
// bind results
        $sql->bind_result($id,$name,$category,$length,$rented);
    

            while($sql->fetch())
                {
                    echo "<tr><td> " . $id. "</td>".
                        "<td>" . $name. "</td>".
                        "<td>". $category. "</td>".
                        "<td> " . $length. "</td>";
                    // "<td>" . $row["rented"]. "</tr>";
                    if ($rented == 0)
                        echo "<td>available</td>";
                    else 
                        echo "<td>checked out</td>";
                    echo "<td>";
                    //echo '<input type="submit" name="deleteItem" value=\''.$row[id].'\'>';
                    echo '<input type="submit" name="'.$id.'" value="Delete">';
                    //echo '<td><input type="submit" name="'.$row['id'].'" value="Delete" /></td>"';
                    echo "</td></tr>";
                }
    }
    echo "</table></form>";
}


?>

</body>
</html>
