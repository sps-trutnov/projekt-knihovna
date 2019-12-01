<?php


require("../renderer.php");
include('../user.php');
session_start();


if(!renderer::isUserLogged())
  returnFail("User is not logged in");


$type = intval(User::Getaccount_typeStatic($_SESSION['username']));

if($type > 0)
  returnFail("Do not have permission");

if(isset($_POST['json']))
{
  $json = $_POST['json'];
  $json = json_decode($json);

  if(isset($json->username) && isset($json->newrole)){

    $return = array();
    $return['success'] = User::SetNewRoleStatic($json->username, $json->newrole);
    $return['role'] = User::Getaccount_typeNameStatic($json->newrole);
    echo json_encode($return);
  }else {
    returnFail("One of fields is not defined");
  }
}else{
  returnFail("Insufficient params");
}