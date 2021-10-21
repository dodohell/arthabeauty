<?php

include("globals.php");

//$settingsObj->checkLogin();

$id = (int) $_REQUEST["id"];
$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
$page_heading = $configVars["email_notifications"];

$php_self = "email_notifications.php";
$php_edit = "email_notifications_ae.php";
$sm->assign("php_self", $php_self);
$sm->assign("php_edit", $php_edit);

$sm->assign("id", $id);
$sm->assign("act", $act);
$sm->assign("menu_pos", $menu_pos);
$sm->assign("page_heading", $page_heading);

$email_notifications = new EmailNotifications();
$settings = new Settings();

//if (isset($_REQUEST["Submit"])) {
//    $email_notifications->addEditRow($_REQUEST);
//    header("Location: email_notifications.php");
//    die();
//}
if ($id) {
    $row = $email_notifications->getRecord($id);
    $sm->assign("row", $row);
}
if ($act == "delete") {
    $field = $_REQUEST["field"];
    $email_notifications->deleteField($id, $field);
    header("Location: email_notifications_ae.php?id=$id&act=edit");
    die();
}

if ($act == "update") {
    $email_notifications->updateTable();
    header("Location: email_notifications.php");
    die();
}
if ($act == "send-customers-profiles-to-recipients") {
    $email_notifications->sendCustomersProfilesToRecipients();
    header("Location: email_notifications.php");
    die();
}

$sm->display("admin/email_notifications_ae.html");
?>