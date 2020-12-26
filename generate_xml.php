<?php
require 'script/connection.php';

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

if(isset($_GET['status']) && $_GET['status'] !='undefined'){	
$status =$_GET['status'];
$query = "SELECT * FROM tbl_lead WHERE status ='$status'";
if($status=='All') {
$query = "SELECT * FROM tbl_lead WHERE status ='Active' OR Status='Approved'";	
}
}else {
	
$query = "SELECT * FROM tbl_lead WHERE status ='Active'";	
}

$result = mysqli_query($conn,$query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}



header("Content-type: text/xml");
// Iterate through the rows, adding XML nodes for each
while ($row = $result->fetch_assoc()) {
$lead_id=$row['lead_id'];
$query2  = "SELECT *  FROM tbl_lead_services WHERE lead_id=$lead_id and ( status ='Approved' OR status ='Active')";
$result2=mysqli_query($conn,$query2);
$services ="";
while ($row2 = $result2->fetch_assoc()) {	 

$services.= $row2['service_name'] .",";
}
	
	//print_r($row);exit;
  // Add to XML document node
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("id",$row['lead_id']);
  $newnode->setAttribute("name",$row['name']);
  $newnode->setAttribute("services",$services);
  $newnode->setAttribute("phone", $row['phone']);
  $newnode->setAttribute("email", $row['email']);
  $newnode->setAttribute("address", $row['address']);
  $newnode->setAttribute("status", $row['status']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lang']); 
  $newnode->setAttribute("type", "restaurant"); 
}
echo $dom->saveXML();
?>
