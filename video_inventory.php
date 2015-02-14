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

// toggle check-in/check-out buttons
function checkAvailabilityToggle(){
    $mysqli = connectToDB();
    if ($id = array_search('check-out',$_POST)){
        // echo $id."<br>";
        $query = "update video_inventory set rented = !rented where id=".$id.";";
        preparedStatement($query);
    } // end check-out search
    else if ($id = array_search('check-in',$_POST)){  // gotta be a cleaner way to do this
        // echo $id."<br>";
        $query = "update video_inventory set rented = !rented where id=".$id.";";
        preparedStatement($query);
    } // end check-out search
}

function resetIDs() {
        // reset counter on primary key
    $mysqli = connectToDB();
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

    if (preparedStatement($query))    
        resetIDs($mysqli);

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
            $query = "insert into video_inventory ".
                "(name,category,length) ".
                "values (\"$name\",\"$category\",\"$length\");";
            // echo "Trying to add $sql";
            //$result = $mysqli->query($sql);
            if (preparedStatement($query))    
                resetIDs();
            return true;
        }
    
    else // name is NULL.  This is NOT okay if category or length are not NULL.
        {
            if ( 
                (isset($_POST["category"]) && $_POST["category"] != null)
                ||
                (isset($_POST["length"]) && $_POST["length"] != null)
            )
                echo "'Name' is a required field.";
        }
    
    return false;
}


// handle post requests via mysql
function checkPostRequests(){
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if (! empty($_POST))
            {
                if(!checkDeletions()) // if we're not deleting
                    if(!checkInsertions())
                        checkAvailabilityToggle();
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


/*
  search database for categories.
  Return an array of categories that can be used to display to user 
  what categories are in the database. 
 */
function getCategories($mysqli){
    $categories = []; // empty container array
    $query = "select distinct category from video_inventory where category!=''";
    
    if ($sql=$mysqli->prepare($query))
        {
            $sql->execute();
            // bind results
            $sql->bind_result($category);
            while($sql->fetch()) // loop over results and push into array
                array_push($categories,$category);
            // print_r($categories);
            return $categories;
        }
}

function drawVideos($mysqli){
    $categories = getCategories($mysqli);
    echo "<form action='' method='post'>";
    echo "<table border=1><tr>".
        "<th>id<th>name".
        "<th>".
        "<select><option value='All Categories'>All Categories</option>";
    // replace this with a string returned from a function that parses categories from a mysql query
    for ($i=0; $i<count($categories); $i++)
        {
            echo "<option value='$categories[$i]'>$categories[$i]</option>";
        }
    echo "</select>".
        "<th>".
        "length<th>rented";
    echo '<th><input type="submit" name="DeleteAll" value="Delete All Videos"></tr>';
    
// prepare statement
    $query = "select id,name,category,length,rented from video_inventory";
    if ($sql=$mysqli->prepare($query)){
        
        $sql->execute();
    
// bind results
        $sql->bind_result($id,$name,$category,$length,$rented);
        // check if category is in filter

            while($sql->fetch())
                {
                    echo "<tr><td> " . $id. "</td>".
                        "<td>" . $name. "</td>".
                        "<td>". $category. "</td>".
                        "<td> " . $length. "</td>";
                    // "<td>" . $row["rented"]. "</tr>";
                    if ($rented == 0)
                        {
                            echo "<td>available</td>";
                            echo "<td>";
                            echo '<input type="submit" name="'.$id.'" value="check-out">';
                            echo '<input type="submit" name="'.$id.'" value="Delete">';
                            echo "</td></tr>";
                        }
                    else 
                        {
                            echo "<td>checked out</td>";
                            echo "<td>";
                            echo '<input type="submit" name="'.$id.'" value="check-in">';
                            echo '<input type="submit" name="'.$id.'" value="Delete">';
                            echo "</td></tr>";
                        }
                }
    }
    echo "</table></form>";
}


?>

</body>
</html>
