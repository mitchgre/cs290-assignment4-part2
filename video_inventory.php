<html>
  <head>
    <title>video inventory</title>
    <script src="video_inventory.js"></script>
  </head>
  <body>


    <form method ='post' action='process_video_inventory.php'>
      <table name='addBar'>
	<tr>
	  <td colspan='2' align='center'> <input type='submit' value='Add Video'></td>
	  <td>name:</td> <td> <input type='text' name='name'></td>
	  <td>category:</td> <td> <input type='text' name='category'></td>
	  <td>length:</td> <td> <input type='text' name='length'></td>
	</tr>
      </table>
    </form>
      
    <form action='' method='post'>
      <table border=1><tr>
        <th>id</th>
	<th>name</th>
	<th>
	  <table>
	    <tr>
	      <td>
		<select name='genreFilter' onchange='this.form.submit()'>
		  <option>Select Category</option>
		  <option>All Categories</option>
		</select>
	      </td>
	    </tr>
	  </table>
	</th>
	<th>length</th>
	<th>rented</th>
	<th><input type='submit' name='DeleteAll' value='Delete All Videos'</th>
      </tr>
	

</body>
</html>
