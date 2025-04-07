<?php
//insert.php;

if(isset($_POST["course"]))
{
 $connect = new PDO("mysql:host=localhost;dbname=aci_leave", "root", "");
 $order_id = uniqid();
 for($count = 0; $count < count($_POST["course"]); $count++)
 {  
  $query = "INSERT INTO tblleave 
  (order_id, course, gred) 
  VALUES (:order_id, :course, :gred)
  ";
  $statement = $connect->prepare($query);
  $statement->execute(
   array(
    ':order_id'   => $order_id,
    ':course'  => $_POST["course"][$count], 
    
    ':gred'  => $_POST["gred"][$count]
   )
  );
 }
 $result = $statement->fetchAll();
 if(isset($result))
 {
  echo 'ok';
 }
}
?>