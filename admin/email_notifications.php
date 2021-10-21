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
$sm->assign("page_heading", $page_heading);

$email_notifications = new EmailNotifications();

if ($act == "delete") {
    $email_notifications->deleteRecord($id);
    header("Location: " . $php_self);
    die();
}

$items = $email_notifications->getEmailNotifications($menu_pos);
$sm->assign("items", $items);

$sm->display("admin/email_notifications.html");
?>