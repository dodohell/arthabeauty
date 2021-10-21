<?php
	include("globals.php");
	
//    $settingsObj->checkLogin();

    // getting statuses wich are representing real sale
    $orderStatusesObj = new OrderStatuses();
    $saleStatuses = $orderStatusesObj->getOrderStatusesSale();
    $saleStatusIds = array_column($saleStatuses, 'id');
    $saleStatusIds = implode(",", $saleStatusIds);
	
    $totalAmounts = $db->getAll("SELECT
                                    *
                                FROM
                                    (
                                        SELECT
                                            DATE(
                                                from_unixtime(carts.postdate)
                                            ) AS DATE,
                                            SUM(carts.total_amount) total_amount,
                                            SUM(carts.total_amount_supply) total_amount_supply,
                                            SUM(carts.total_amount_profit) total_amount_profit,
                                            SUM(carts.delivery_amount) total_delivery_amount
                                        FROM
                                            {$carts_table} AS carts
                                        WHERE
                                            carts.edate = 0
                                        AND carts.finalised = 1
                                        AND carts.status IN (".$saleStatusIds.")
                                        GROUP BY
                                            DATE(
                                                from_unixtime(carts.postdate)
                                            )
                                        ORDER BY
                                            DATE DESC
                                        LIMIT 30
                                    ) AS sub
                                ORDER BY
                                    DATE ASC");
                                            
//    $totalAmountsBegin = date_create($totalAmounts[0]["DATE"]);
    $totalAmountsBegin = date_create(date("Y-m-d", strtotime('-30 day')));
//    $totalAmountsEnd = date_create(end($totalAmounts)["DATE"]);
    $totalAmountsEnd = date_create(date("Y-m-d", strtotime('now')));
    $totalAmountsEnd->setTime(0,0,1);
    $i = new DateInterval('P1D');
    $totalAmountsPeriod = new DatePeriod($totalAmountsBegin,$i,$totalAmountsEnd);  
    $totalAmountsNew = array();
    
    foreach ($totalAmountsPeriod as $k => $v) {
        $day = $v->format('Y-m-d');
        foreach ($totalAmounts as $kk => $vv) {
            if($vv["DATE"] == $day){
                $totalAmountsNew[$day] = $totalAmounts[$kk];
                break;
            }else{
                $totalAmountsNew[$day] = array(
                    "DATE" => $day,
                    "total_amount" => "0.00",
                    "total_amount_supply" => "0.00",
                    "total_amount_profit" => "0.00"
                );
            }
        }
    }
    $totalAmounts = array_values($totalAmountsNew);
    
    $sm->assign("totalAmounts", $totalAmounts);
    
    
    $currentDay = date("Y-m-d");
    $currentDayOrders = $db->getAll("SELECT
                                        carts.id,
                                        DATE(
                                            from_unixtime(carts.postdate)
                                        ) AS DATE,
                                        carts.postdate,
                                        from_unixtime(carts.postdate) AS orderDatetime,
                                        carts.total_amount,
                                        carts.total_amount_supply,
                                        carts.total_amount_profit,
                                        carts.delivery_amount
                                    FROM
                                        {$carts_table} AS carts
                                    WHERE
                                        carts.edate = 0
                                    AND carts.finalised = 1
                                    AND carts.status IN (".$saleStatusIds.")
                                    HAVING DATE = '{$currentDay}'
                                    ORDER BY
                                        orderDatetime DESC");
    $currentDayTotalAmount = 0;
    $currentDayTotalAmountSupply = 0;
    $currentDayTotalAmountProfit = 0;
    $currentDayTotalDeliveryAmount = 0;
    foreach ($currentDayOrders as $k => $v) {
        $currentDayTotalAmount += $v["total_amount"];
        $currentDayTotalAmountSupply += $v["total_amount_supply"];
        $currentDayTotalAmountProfit += $v["total_amount_profit"];
        $currentDayTotalDeliveryAmount += $v["delivery_amount"];
    }
    
    $sm->assign("currentDayTotalAmount", $currentDayTotalAmount);
    $sm->assign("currentDayTotalAmountSupply", $currentDayTotalAmountSupply);
    $sm->assign("currentDayTotalAmountProfit", $currentDayTotalAmountProfit);
    $sm->assign("currentDayTotalDeliveryAmount", $currentDayTotalDeliveryAmount);
    
    $sm->assign("currentDayOrders", $currentDayOrders);
    
    
    $lastDay = date("Y-m-d", strtotime('-1 day'));
    $lastDayOrders = $db->getAll("SELECT
                                        carts.id,
                                        DATE(
                                            from_unixtime(carts.postdate)
                                        ) AS DATE,
                                        carts.postdate,
                                        from_unixtime(carts.postdate) AS orderDatetime,
                                        carts.total_amount,
                                        carts.total_amount_supply,
                                        carts.total_amount_profit,
                                        carts.delivery_amount
                                    FROM
                                        {$carts_table} AS carts
                                    WHERE
                                        carts.edate = 0
                                    AND carts.finalised = 1
                                    AND carts.status IN (".$saleStatusIds.")
                                    HAVING DATE = '{$lastDay}'
                                    ORDER BY
                                        orderDatetime DESC");
    $lastDayTotalAmount = 0;
    $lastDayTotalAmountSupply = 0;
    $lastDayTotalAmountProfit = 0;
    $lastDayTotalDeliveryAmount = 0;
    foreach ($lastDayOrders as $k => $v) {
        $lastDayTotalAmount += $v["total_amount"];
        $lastDayTotalAmountSupply += $v["total_amount_supply"];
        $lastDayTotalAmountProfit += $v["total_amount_profit"];
        $lastDayTotalDeliveryAmount += $v["delivery_amount"];
    }
    
    $sm->assign("lastDayTotalAmount", $lastDayTotalAmount);
    $sm->assign("lastDayTotalAmountSupply", $lastDayTotalAmountSupply);
    $sm->assign("lastDayTotalAmountProfit", $lastDayTotalAmountProfit);
    $sm->assign("lastDayTotalDeliveryAmount", $lastDayTotalDeliveryAmount);
    
    $sm->assign("lastDayOrders", $lastDayOrders);
    
    $totalAmountsCurrentMonth = $db->getAll("SELECT
                                                *
                                            FROM
                                                (
                                                    SELECT
                                                        DATE(
                                                            from_unixtime(carts.postdate)
                                                        ) AS DATE,
                                                        SUM(carts.total_amount) total_amount,
                                                        SUM(carts.total_amount_supply) total_amount_supply,
                                                        SUM(carts.total_amount_profit) total_amount_profit,
                                                        SUM(carts.delivery_amount) total_delivery_amount,
                                                        COUNT(id) total_orders_count
                                                    FROM
                                                        {$carts_table} AS carts
                                                    WHERE
                                                        carts.edate = 0
                                                    AND carts.finalised = 1
                                                    AND carts.status IN (".$saleStatusIds.")
                                                    GROUP BY
                                                        DATE(
                                                            from_unixtime(carts.postdate)
                                                        )
                                                    ORDER BY
                                                        DATE DESC
                                                    LIMIT 31
                                                ) AS sub
                                            ORDER BY
                                                DATE ASC");
                                            
    $totalAmountsCurrentMonthBegin = date_create(date('Y-m-01'));
    $totalAmountsCurrentMonthEnd = date_create(date("Y-m-d", strtotime('now')));
    $totalAmountsCurrentMonthEnd->setTime(0,0,1);
    $i = new DateInterval('P1D');
    $totalAmountsCurrentMonthPeriod = new DatePeriod($totalAmountsCurrentMonthBegin,$i,$totalAmountsCurrentMonthEnd);  
    $totalAmountsCurrentMonthNew = array();
    
    
    $currentMonthTotalAmount = 0;
    $currentMonthTotalAmountSupply = 0;
    $currentMonthTotalAmountProfit = 0;
    $currentMonthTotalDeliveryAmount = 0;
    $currentMonthOrdersCount = 0;
    foreach ($totalAmountsCurrentMonthPeriod as $k => $v) {
        $day = $v->format('Y-m-d');
        foreach ($totalAmountsCurrentMonth as $kk => $vv) {
            if($vv["DATE"] == $day){
                $totalAmountsCurrentMonthNew[$day] = $totalAmountsCurrentMonth[$kk];
                $currentMonthTotalAmount += $totalAmountsCurrentMonth[$kk]["total_amount"];
                $currentMonthTotalAmountSupply += $totalAmountsCurrentMonth[$kk]["total_amount_supply"];
                $currentMonthTotalAmountProfit += $totalAmountsCurrentMonth[$kk]["total_amount_profit"];
                $currentMonthTotalDeliveryAmount += $totalAmountsCurrentMonth[$kk]["total_delivery_amount"];
                $currentMonthOrdersCount += $totalAmountsCurrentMonth[$kk]["total_orders_count"];
                break;
            }else{
                $totalAmountsCurrentMonthNew[$day] = array(
                    "DATE" => $day,
                    "total_amount" => "0.00",
                    "total_amount_supply" => "0.00",
                    "total_amount_profit" => "0.00",
                    "total_delivery_amount" => "0.00",
                    "total_orders_count" => "0"
                );
            }
        }
    }
    $totalAmountsCurrentMonth = array_values($totalAmountsCurrentMonthNew);
    
    $sm->assign("currentMonthTotalAmount", $currentMonthTotalAmount);
    $sm->assign("currentMonthTotalAmountSupply", $currentMonthTotalAmountSupply);
    $sm->assign("currentMonthTotalAmountProfit", $currentMonthTotalAmountProfit);
    $sm->assign("currentMonthTotalDeliveryAmount", $currentMonthTotalDeliveryAmount);
    $sm->assign("currentMonthOrdersCount", $currentMonthOrdersCount);
    
    $sm->assign("totalAmountsCurrentMonth", $totalAmountsCurrentMonth);
    
    
    
    $totalAmountsLastMonth = $db->getAll("SELECT
                                    *
                                FROM
                                    (
                                        SELECT
                                            DATE(
                                                from_unixtime(carts.postdate)
                                            ) AS DATE,
                                            SUM(carts.total_amount) total_amount,
                                            SUM(carts.total_amount_supply) total_amount_supply,
                                            SUM(carts.total_amount_profit) total_amount_profit,
                                            SUM(carts.delivery_amount) total_delivery_amount,
                                            COUNT(id) total_orders_count
                                        FROM
                                            {$carts_table} AS carts
                                        WHERE
                                            carts.edate = 0
                                        AND carts.finalised = 1
                                        AND carts.status IN (".$saleStatusIds.")
                                        GROUP BY
                                            DATE(
                                                from_unixtime(carts.postdate)
                                            )
                                        ORDER BY
                                            DATE DESC
                                        LIMIT 31
                                    ) AS sub
                                ORDER BY
                                    DATE ASC");
                                            
//    $totalAmountsLastMonthBegin = date_create(date('Y-m-01'));
//    $totalAmountsLastMonthEnd = date_create(date("Y-m-d", strtotime('now')));
    $totalAmountsLastMonthBegin = date_create("first day of last month");
    $totalAmountsLastMonthEnd = date_create("last day of last month");
    $totalAmountsLastMonthEnd->setTime(0,0,1);
    $i = new DateInterval('P1D');
    $totalAmountsLastMonthPeriod = new DatePeriod($totalAmountsLastMonthBegin,$i,$totalAmountsLastMonthEnd);  
    $totalAmountsLastMonthNew = array();
    
    
    $lastMonthTotalAmount = 0;
    $lastMonthTotalAmountSupply = 0;
    $lastMonthTotalAmountProfit = 0;
    $lastMonthTotalDeliveryAmount = 0;
    $lastMonthOrdersCount = 0;
    foreach ($totalAmountsLastMonthPeriod as $k => $v) {
        $day = $v->format('Y-m-d');
        foreach ($totalAmountsLastMonth as $kk => $vv) {
            if($vv["DATE"] == $day){
                $totalAmountsLastMonthNew[$day] = $totalAmountsLastMonth[$kk];
                $lastMonthTotalAmount += $totalAmountsLastMonth[$kk]["total_amount"];
                $lastMonthTotalAmountSupply += $totalAmountsLastMonth[$kk]["total_amount_supply"];
                $lastMonthTotalAmountProfit += $totalAmountsLastMonth[$kk]["total_amount_profit"];
                $lastMonthTotalDeliveryAmount += $totalAmountsLastMonth[$kk]["total_delivery_amount"];
                $lastMonthOrdersCount += $totalAmountsLastMonth[$kk]["total_orders_count"];
                break;
            }else{
                $totalAmountsLastMonthNew[$day] = array(
                    "DATE" => $day,
                    "total_amount" => "0.00",
                    "total_amount_supply" => "0.00",
                    "total_amount_profit" => "0.00",
                    "total_delivery_amount" => "0.00",
                    "total_orders_count" => "0"
                );
            }
        }
    }
    $totalAmountsLastMonth = array_values($totalAmountsLastMonthNew);

    $sm->assign("lastMonthTotalAmount", $lastMonthTotalAmount);
    $sm->assign("lastMonthTotalAmountSupply", $lastMonthTotalAmountSupply);
    $sm->assign("lastMonthTotalAmountProfit", $lastMonthTotalAmountProfit);
    $sm->assign("lastMonthTotalDeliveryAmount", $lastMonthTotalDeliveryAmount);
    $sm->assign("lastMonthOrdersCount", $lastMonthOrdersCount);
    
    $sm->assign("totalAmountsLastMonth", $totalAmountsLastMonth);
    
    //$totalAmountsLabels = array_column($totalAmounts, 'DATE');
    //$totalAmountsValues = array_column($totalAmounts, 'total_amount');
    //$sm->assign("totalAmountsLabels", $totalAmountsLabels);
    //$sm->assign("totalAmountsValues", $totalAmountsValues);
    
//	$id = (int)$_REQUEST["id"];
//	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
//	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
//	$page_heading = $configVars["menus"];
//	
//	$php_self = "menus.php";
//	$php_edit = "menus_ae.php";
//	$sm->assign("php_self", $php_self);
//	$sm->assign("php_edit", $php_edit);
//	
//	$sm->assign("id", $id);
//	$sm->assign("act", $act);
//	$sm->assign("menu_pos", $menu_pos);
//	$sm->assign("page_heading", $page_heading);
//	
//	$menus = new Menus();
//	$items = $menus->getMenuPosition($menu_pos);
//	$sm->assign("items", $items);
    
	$sm->display("admin/dashboard.html");
?>