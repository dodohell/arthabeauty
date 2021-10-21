<!DOCTYPE html>
<html lang="bg">
    <head>
        <title>New Project</title>
        <meta charset=utf-8>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
        <link rel="stylesheet" type="text/css" href="css/jquery.fancybox.min.css">
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="css/select2.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="js/swiper.min.js"></script>
        <script src="js/jquery.fancybox.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <script src="js/jquery.ui.touch-punch.min.js"></script>
        <script src="js/select2.min.js"></script>

    </head>
    <?php
//Tova e za globalsa
    require "lessc.inc.php";

    $less = new lessc;
    $less->checkedCompile("css/style.less", "css/style.css");
    ?>
    <body>

        <header>

            <div class="mobileHeader only-mobile">
                <div class="container">
                    <div class="mobRow">
                        <span id="searchIcon"></span>
                        <a class="logoMobile" href="/"></a>
                        <div class="iconsBox">
                            <a href="" class="cart">
                                <span class="imgHolder"></span>
                                <span class="quantity">5</span>
                            </a>
                            <div id="menuToggle">
                                <input type="checkbox" />
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="topLine">
                <div class="container">
                    <div class="headRow">
                        <div class="langBox">
                            <a class="" href=""><img src="images/icon-bg.png" alt=""></a>
                            <a class="active" href=""><img src="images/icon-en.png" alt=""></a>
                        </div>
                        <div class="usersContent">
                            <div class="login">
                                <div class="notLoggedIn">
                                    <a data-animation-duration="700" data-fancybox data-src="#loginForm" href="javascript:;">Вход</a><span>или</span><a data-animation-duration="700" data-fancybox data-src="#signUpForm" href="javascript:;">Регистрация</a>
                                </div>
                                <div class="loggedIn"><a href="#"><div class="imgContainer"><img src="images/icon-profile.svg" alt=""></div>Профил</a></div>

                                <div style="display: none;" id="signUpForm" class="animated-modal">
                                    <h2 data-selectable="true">Регистрация</h2>
                                    <div data-selectable="true" class="fancyBoxContent">
                                        <a class="facebookLogin" href="#">Регистрация с facebook</a>
                                        <a class="googleLogin" href="#">Регистрация с Google</a>
                                        <div class="orSeparator">
                                            <span class="text">или</span>
                                            <span class="line"></span>
                                        </div>
                                        <form>
                                            <div class="inputRow">
                                                <div class="inputCol6">
                                                    <input type="text" name="fname" placeholder="Имe">
                                                </div>
                                                <div class="inputCol6">
                                                    <input type="text" name="lname" placeholder="Фамилия">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="text" name="email" placeholder="Имейл">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="text" name="password" placeholder="Парола">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <label>
                                                        <input type="checkbox" name="subscribe">
                                                        <span>Да, искам да се абонирам за ексклузивните оферти на Arthabeauty</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="submit" class="submitBtn" value="Регистрирай се">
                                                </div>
                                            </div>
                                        </form>
                                        <div class="loginBox">
                                            Имаш акаунт? <a class="loginBtn" href="#">Вход</a>
                                        </div>
                                        <div class="termsBox">
                                            <a href="#">Политика на конфиденциалност</a>
                                            <a href="#">Условия на ползване</a>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: none;" id="loginForm" class="animated-modal">
                                    <h2 data-selectable="true">Вход</h2>
                                    <div data-selectable="true" class="fancyBoxContent">
                                        <a class="facebookLogin" href="#">Продължи с facebook</a>
                                        <a class="googleLogin" href="#">Продължи с Google</a>
                                        <div class="orSeparator">
                                            <span class="text">или</span>
                                            <span class="line"></span>
                                        </div>
                                        <form>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="text" name="email" placeholder="Имейл">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="text" name="password" placeholder="Парола">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <div class="forgotPass">
                                                        <a data-animation-duration="700" data-fancybox data-src="#forgotPassForm" href="javascript:;" href="#">Забравена парола?</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="submit" class="submitBtn" value="Регистрирай се">
                                                </div>
                                            </div>
                                        </form>
                                        <div class="loginBox">
                                            Нямаш акаунт? <a class="loginBtn" href="#">Регистрирай се</a>
                                        </div>
                                        <div class="termsBox">
                                            <a href="#">Политика на конфиденциалност</a>
                                            <a href="#">Условия на ползване</a>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: none;" id="forgotPassForm" class="animated-modal">
                                    <h2 data-selectable="true">Забравена парола</h2>
                                    <div data-selectable="true" class="fancyBoxContent">
                                        <div class="instruction">Моля въведете своя email.</div>
                                        <form>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="text" name="email" placeholder="Имейл">
                                                </div>
                                            </div>
                                            <div class="inputRow">
                                                <div class="inputCol12">
                                                    <input type="submit" class="submitBtn" value="Прати парола">
                                                </div>
                                            </div>
                                        </form>
                                        <div class="termsBox">
                                            <a href="#">Политика на конфиденциалност</a>
                                            <a href="#">Условия на ползване</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="iconsBox">
                                <a href="" class="cart">
                                    <span class="imgHolder"></span>
                                    <span class="quantity">5</span>
                                    <div class="cartItemsWrapper">
                                        <div class="cartItemsList">
                                            <div class="headingRow">
                                                <span class="headTitle">Количка</span>
                                                <span class="headText">5 продуктa</span>
                                            </div>
                                            <ul class="products">
                                                <li>
                                                    <div class="productImage">
                                                        <div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
                                                    </div>
                                                    <div class="productInfo">
                                                        <span class="name">Продукт дълго име Продукт дълго име Продукт дълго име</span>
                                                        <span class="excerpt">Късо Описание Късо Описание Късо Описание</span>
                                                    </div>
                                                    <div class="productPrice">
                                                        99.99 лв 
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="productImage">
                                                        <div class="image" style="background-image: url(images/top-cat-1.jpg);"></div>
                                                    </div>
                                                    <div class="productInfo">
                                                        <span class="name">Продукт дълго име</span>
                                                        <span class="excerpt">Късо Описание Късо Описание Късо Описание</span>
                                                    </div>
                                                    <div class="productPrice">
                                                        3 x 242.99 лв 
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="productImage">
                                                        <div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
                                                    </div>
                                                    <div class="productInfo">
                                                        <span class="name">Продукт дълго име Продукт дълго име Продукт дълго име</span>
                                                        <span class="excerpt">Късо Описание Късо Описание Късо Описание</span>
                                                    </div>
                                                    <div class="productPrice">
                                                        99.99 лв 
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="productImage">
                                                        <div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
                                                    </div>
                                                    <div class="productInfo">
                                                        <span class="name">Продукт дълго име Продукт дълго име Продукт дълго име</span>
                                                        <span class="excerpt">Късо Описание Късо Описание Късо Описание</span>
                                                    </div>
                                                    <div class="productPrice">
                                                        99.99 лв 
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="cartFooter">
                                                <span class="total">Общо:<strong>198.99 лв</strong></span>
                                                <span class="checkout">Плати</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="" class="hearth">
                                    <span class="imgHolder"></span>
                                    <span class="quantity">15</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottomLine">
                <div class="container">
                    <nav class="headRow">
                        <div class="logoBox"><a href="/"><img src="images/logo.png" alt=""></a></div>
                        <div class="menuHolder">
                            <ul class="menu">
                                <li class="menuLi only-mobile favoriteItems">
                                    <a class="menuLink" href=""><span class="imgContainer"><img src="images/icon-hearth.png" alt=""></span>любими</a>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink hasSubMenu" href="">Жени</a>
                                    <div class="submenuHolder">
                                        <ul class="submenu">
                                            <li class="submenuLi"><a href="">Аромати0</a></li>
                                            <li class="submenuLi"><a href="">Грижа за коса</a></li>
                                            <li class="submenuLi"><a href="">Грижа за кожа</a></li>
                                            <li class="submenuLi"><a href="">Слънце и тен</a></li>
                                            <li class="submenuLi"><a href="">Баня и тяло</a></li>
                                            <li class="submenuLi"><a href="">Грижа за уста</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink hasSubMenu" href="">Мъже</a>
                                    <div class="submenuHolder">
                                        <ul class="submenu">
                                            <li class="submenuLi"><a href="">Аромати9</a></li>
                                            <li class="submenuLi"><a href="">Грижа за коса</a></li>
                                            <li class="submenuLi"><a href="">Грижа за кожа</a></li>
                                            <li class="submenuLi"><a href="">Слънце и тен</a></li>
                                            <li class="submenuLi"><a href="">Баня и тяло</a></li>
                                            <li class="submenuLi"><a href="">Грижа за уста</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink hasSubMenu" href="">Деца</a>
                                    <div class="submenuHolder">
                                        <ul class="submenu">
                                            <li class="submenuLi"><a href="">Аромати7</a></li>
                                            <li class="submenuLi"><a href="">Грижа за коса</a></li>
                                            <li class="submenuLi"><a href="">Грижа за кожа</a></li>
                                            <li class="submenuLi"><a href="">Слънце и тен</a></li>
                                            <li class="submenuLi"><a href="">Баня и тяло</a></li>
                                            <li class="submenuLi"><a href="">Грижа за уста</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink hasSubMenu" href="">Брандове</a>
                                    <div class="submenuHolder">
                                        <ul class="submenu">
                                            <li class="submenuLi"><a href="">Аромати1</a></li>
                                            <li class="submenuLi"><a href="">Грижа за коса</a></li>
                                            <li class="submenuLi"><a href="">Грижа за кожа</a></li>
                                            <li class="submenuLi"><a href="">Слънце и тен</a></li>
                                            <li class="submenuLi"><a href="">Баня и тяло</a></li>
                                            <li class="submenuLi"><a href="">Грижа за уста</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink" href="">Промоции</a>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink" href="">Блог</a>
                                </li>
                                <li class="menuLi">
                                    <a class="menuLink" href="">ArthaBeauty Consultant</a>
                                </li>
                            </ul>
                        </div>
                        <form class="searchHolder" action="">
                            <input type="text" name="searchInput" placeholder="Търси...">
                            <input class="submitBtn" type="submit" value="">
                        </form>
                    </nav>
                </div>
            </div>
        </header>



        <!--=====================================
        =        End of Header section          =
        ======================================-->




        <div class="breadcrumbsContainer">
            <div class="container">
                <div class="breadcrubsRow">
                    <a href="">Начало</a>
                    <span>|</span>
                    <a href="">Аромати</a> 
                </div>
            </div>
        </div>

        <div class="catBackgroundGradient">

            <div class="bannerTop">
                <div class="container">
                    <div class="backgroundImage" style="background-image: url(images/cat-banner.jpg);">
                        <h2 class="heading">Промоция на <span class="stronger">Всички Мъжки Аромати!</span></h2>
                        <a class="goTo" href="">Пазарувай Сега</a>
                        <span class="discount">
                            20% <br> OFF
                        </span>
                    </div>
                </div>
            </div>

            <div class="mobileCategories">
                <div class="container">
                    <div class="headButtons">
                        <div class="topRow">
                            <span class="catTopBtn" href="#">Аромати <span class="icon"><img src="images/arrow-down-gray.svg" alt=""></span></span>
                            <ul class="listCat">
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                            </ul>
                        </div>
                        <div class="downSection">
                            <div class="bottomRow">
                                <span class="leftSortBtn">СОРТИРАЙ <span class="icon"><img src="images/arrow-down-gray.svg" alt=""></span></span>
                                <span class="filterBtn">ФИЛТРИ</span>
                            </div>
                            <div class="hiddenSections">
                                <div class="hiddenSort">
                                    <a href="">Най нови</a>
                                    <a href="">Най скъпи</a>
                                    <a href="">Най яки</a>
                                </div>
                                <div class="filterWrapper">
                                    <form id="hiddenFilter">
                                        <ul class="hiddenFiltersList">
                                            <li class="filterLi closeFilter"><span class="closeFilterBtn">Филтри</span> <span class="clearWholeForm" onclick="uncheckAllcheckboxes('#hiddenFilter')">изчисти</span></li>
                                            <li class="filterLi">
                                                <span class="filterLink hasSubFilter" href="">Производител</span>
                                                <span class="selectedItems"></span>
                                                <div id="index1" class="subFilterHolder">
                                                    <ul class="subFilter">
                                                        <li class="subfilterLi">
                                                            <label>Производител 1
                                                                <input type="checkbox" name="option1[]" value="Производител 1">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Производител 2
                                                                <input type="checkbox" name="option1[]" value="Производител 2">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Производител 3
                                                                <input type="checkbox" name="option1[]" value="Производител 3">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Производител 4
                                                                <input type="checkbox" name="option1[]" value="Производител 4">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Производител 5
                                                                <input type="checkbox" name="option1[]" value="Производител 5">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Производител 6
                                                                <input type="checkbox" name="option1[]" value="Производител 6">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="filterLi">
                                                <span class="filterLink hasSubFilter" href="">Разфасовка</span>
                                                <span class="selectedItems"></span>
                                                <div id="index2" class="subFilterHolder">
                                                    <ul class="subFilter">
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 1
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 1">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 2
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 2">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 3
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 3">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 4
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 4">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 5
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 5">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Разфасовка 6
                                                                <input type="checkbox" name="option2[]" value="Разфасовка 6">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="filterLi">
                                                <span class="filterLink hasSubFilter" href="">Предназначение</span>
                                                <span class="selectedItems"></span>
                                                <div id="index3" class="subFilterHolder">
                                                    <ul class="subFilter">
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 1
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 2
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 3
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 4
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 5
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Предназначение 6
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="filterLi">
                                                <span class="filterLink hasSubFilter" href="">Специфика</span>
                                                <span class="selectedItems"></span>
                                                <div id="index4" class="subFilterHolder">
                                                    <ul class="subFilter">
                                                        <li class="subfilterLi">
                                                            <label>Специфика 1
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Специфика 2
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Специфика 3
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Специфика 4
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Специфика 5
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                        <li class="subfilterLi">
                                                            <label>Специфика 6
                                                                <input type="checkbox">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="filterLi">
                                                <span class="filterLink hasSubFilter" href="">Цена</span>
                                                <span id="priceTextFilter" class="selectedItems"></span>
                                                <div id="index5" class="subFilterHolder">
                                                    <ul class="subFilter">
                                                        <li class="subfilterLi">
                                                        </li>
                                                    </ul>
                                                    <div class="selector">
                                                        <div class="price-slider">
                                                            <div id="slider-range2" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                                                <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
                                                                <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
                                                            </div>
                                                            <span id="min-price2" data-currency="лв." class="slider-price">0</span> 
                                                            <span id="max-price2" data-currency="лв." data-max="300"  class="slider-price">300</span>    
                                                        </div> 
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <button id="mobileFilterSubmit">Покажи продукти</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="categoriesWrapper">
                <div class="container">
                    <div class="filterContainer">
                        <div class="subCategories">
                            <span class="heading">Аромати</span>
                            <ul class="listCat">
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                                <li>
                                    <a href="#">Подкатегория</a>
                                </li>
                            </ul>
                        </div>
                        <div class="formContainer">
                            <form>
                                <span class="topTitle">Филтри</span>
                                <div class="formSection">
                                    <span class="heading">Производител</span>
                                    <label>
                                        Производител
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Производител
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Производител
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Производител
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Производител
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>

                                <div class="formSection">
                                    <span class="heading">Разфасовка</span>
                                    <label>
                                        Разфасовка
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Разфасовка
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Разфасовка
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Разфасовка
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Разфасовка
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>

                                <div class="formSection">
                                    <span class="heading">Предназначение</span>
                                    <label>
                                        Предназначение
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Предназначение
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Предназначение
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Предназначение
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Предназначение
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>

                                <div class="formSection">
                                    <span class="heading">Специфика</span>
                                    <label>
                                        Специфика
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Специфика
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Специфика
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Специфика
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label>
                                        Специфика
                                        <input type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>

                                <div class="selector">
                                    <div class="price-slider">
                                        <div id="slider-range" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                            <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
                                            <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
                                        </div>
                                        <span id="min-price" data-currency="лв." class="slider-price">0</span> 
                                        <span id="max-price" data-currency="лв." data-max="300"  class="slider-price">300</span>    
                                    </div> 
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="productsSection">
                        <div class="topRow">
                            <div class="heading">Продукти <span class="subHeading">(1000)</span></div>
                            <div class="sortByContainer">
                                <span class="text">Сортиране по:</span>
                                <div class="sortByBox">
                                    <select class="sortBy">
                                        <option>Най нови</option>
                                        <option>Най нови</option>
                                        <option>Най скъпи</option>
                                        <option>Най скъпи</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="productsRow">
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod1.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod2.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod3.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod4.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod5.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod6.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod7.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod1.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod2.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod3.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod4.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod5.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod6.jpg);">
                                        <span class="likedBox likedBoxRed"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod7.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod4.jpg);">
                                        <span class="likedBox likedBoxRed"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                            <div class="singleItem">
                                <a class="product" href="">
                                    <div class="imgContainer" style="background-image: url(images/cat-prod5.jpg);">
                                        <span class="likedBox"></span>
                                    </div>
                                    <h3 class="name">Продукт дълго име на два реда</h3>
                                    <span class="excerpt">Описание Късо Описание на два реда</span>
                                    <span class="price">80.00лв</span>
                                </a>
                                <div class="starsRow">
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                    <span class="starBox"><img src="images/star.svg" alt=""></span>
                                </div>
                            </div>
                        </div>
                        <div class="bottomRow">
                            <div class="excerpt">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vulputate fermentum nec aliquet mattis. Varius egestas purus at curabitur. Tortor, pellentesque morbi ac, varius sed aliquam suspendisse. Felis vestibulum et varius non habitasse ut.
                            </div>
                            <div class="pagination">
                                <a class="active" href="">1</a>
                                <a href="">2</a>
                                <a href="">3</a>
                                <a href="">4</a>
                                <a href="">5</a>
                                <a href="">6</a>
                                <a href="">7</a>
                                <span>.</span>
                                <span>.</span>
                                <span>.</span>
                                <a href="">10</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>

                function addCommas(nStr) {
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';
                    var rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + ',' + '$2');
                    }
                    return x1 + x2;
                }


                $(document).ready(function () {

                    $("#slider-range").slider({
                        range: true,
                        min: 0,
                        max: 300,
                        step: 1,
                        slide: function (event, ui) {
                            $("#min-price").html(addCommas(ui.values[ 0 ]));

                            suffix = '';
                            if (ui.values[ 1 ] == $("#max-price").data('max')) {
                                suffix = ' +';
                            }
                            $("#max-price").html(addCommas(ui.values[ 1 ] + suffix));
                        }
                    });

                    $("#slider-range2").slider({
                        range: true,
                        min: 0,
                        max: 300,
                        step: 1,
                        slide: function (event, ui) {
                            $("#min-price2").html(addCommas(ui.values[ 0 ]));

                            suffix = '';
                            if (ui.values[ 1 ] == $("#max-price2").data('max')) {
                                suffix = ' +';
                            }
                            $("#max-price2").html(addCommas(ui.values[ 1 ] + suffix));

                            $("#priceTextFilter").html(addCommas(ui.values[ 0 ]) + "лв. - " + addCommas(ui.values[ 1 ] + suffix + " лв."));
                            $("#priceTextFilter").parent().addClass("height70");
                        }
                    });

                });

            </script>



            <!--=====================================
            =             Footer Section            =
            ======================================-->



            <script>

                var updated = 0;
                var updated2 = 0;

                $(document).ready(function () {

                    $("#menuToggle").bind("click", function () {

                        if ($(this).children("input").is(":checked")) {
                            if (updated == 0) {
                                updated = 1;
                            }
                            $(".bottomLine .container .headRow").css("height", "0px");
                            $("#searchIcon").removeClass("searchIconActive");
                            $(".searchHolder").removeClass("menuShow");
                            $("nav.headRow").addClass("overflowVisible");
                            $(".topLine").addClass("overflowVisible2");
                            $(".topLine .container .headRow").addClass("menuShow");
                            $(".menuHolder").addClass("menuShow");
                        } else {

                            $("nav.headRow").removeClass("overflowVisible");
                            $(".topLine").removeClass("overflowVisible2");
                            $(".topLine .container .headRow").removeClass("menuShow");
                            $(".menuHolder").removeClass("menuShow");
                        }
                    });

                    $("#searchIcon").bind('click', function (e) {
                        if ($(".searchHolder").hasClass("menuShow")) {
                            $(".bottomLine .container .headRow").css("height", "0px");
                            $("#searchIcon").removeClass("searchIconActive");
                            $(".searchHolder").removeClass("menuShow");
                        } else {
                            if ($("#menuToggle").children("input").is(":checked")) {
                                $("#menuToggle").find("input").trigger("click");
                            }
                            $("#searchIcon").addClass("searchIconActive");
                            $(".bottomLine .container .headRow").css("height", "64px");
                            $(".searchHolder").addClass("menuShow");
                        }

                    });

                    $(".hasSubMenu").bind('click', function (e) {

                        if ($(".only-mobile").css("display") == "block") {
                            e.preventDefault();

                            var menuName = $(this).text();

                            $(".submenuHolder").removeClass("menuShow");

                            if ($(this).parent().children(".submenuHolder").hasClass("menuShow")) {

                                $(".submenuHolder").removeClass("menuShow");
                            } else {

                                if (updated2 == 0) {
                                    updated2 = 1;
                                }

                                if ($(this).parent().children(".submenuHolder").children(".submenu").children(".submenuLi").hasClass("subMenubBackBtn")) {
                                } else {

                                    $(this).parent().children(".submenuHolder").children(".submenu").prepend("<li class='submenuLi subMenubBackBtn'><span>" + menuName + "</span> </li>");
                                }

                                $(this).parent().children(".submenuHolder").addClass("menuShow");

                                $(".subMenubBackBtn").bind("click", function () {

                                    $(this).parent().parent().removeClass("menuShow");

                                });
                            }
                        }
                    });

                    var swiper = new Swiper('#swiper-container1', {
                        slidesPerView: 1,
                        loop: true,
                        autoplay: {
                            delay: 2500,
                        },
                    });

                    $('.sortBy').select2({
                        minimumResultsForSearch: -1,
                    });

                });

            </script>



            <!-- SCRIPT FOR MOBILE  -->


            <script>

                var updated = 0;
                var updated2 = 0;
                var updated3 = 0;

                $(document).ready(function () {

                    $(".catTopBtn").bind("click", function () {

                        if ($(".only-mobile").css("display") == "block") {
                            if ($(this).next(".listCat").css("display") == "none") {
                                if (updated2 == 0) {
                                    updated2 = 1;
                                }
                                $(".leftSortBtn").children(".icon").removeClass("rotateIcon");
                                $(".hiddenSort").slideUp();

                                $(this).children(".icon").addClass("rotateIcon");
                                $(this).next(".listCat").slideDown();
                            } else {
                                $(this).children(".icon").removeClass("rotateIcon");
                                $(this).next(".listCat").slideUp();
                            }
                        }
                    });

                    $(".leftSortBtn").bind("click", function () {

                        if ($(".only-mobile").css("display") == "block") {
                            if ($(".hiddenSort").css("display") == "none") {
                                if (updated3 == 0) {
                                    updated3 = 1;
                                }
                                $(".catTopBtn").children(".icon").removeClass("rotateIcon");
                                $(".listCat").slideUp();

                                $(this).children(".icon").addClass("rotateIcon");
                                $(".hiddenSort").slideDown();
                            } else {
                                $(this).children(".icon").removeClass("rotateIcon");
                                $(".hiddenSort").slideUp();
                            }
                        }
                    });

                    $(".filterBtn").bind("click", function () {
                        $(".filterWrapper").removeClass("zindex-1");
                        $(".filterWrapper").addClass("overflowVisible");
                        $("#hiddenFilter").addClass("menuShow");
                    });


                    $(".subfilterLi label").bind('click', function (event) {
                        var selectorId = $(this).parent().parent().parent().attr('id');
                        var textHolder = $("#" + selectorId).parent().children(".selectedItems");
                        var deleteBtn = $("#" + selectorId).children(".subFilter").children(".subFilterBackBtnHolder").children(".clearFilter");
                        var parentContainer = $("#" + selectorId).parent();
                        if ($(this).children("input").is(':checked')) {
                            $(this).addClass("checkedLabel");
                        } else {
                            $(this).removeClass("checkedLabel");
                        }
                        var selectedItems = get_all_checkbox_values(selectorId);
                        $(textHolder).empty();

                        if (selectedItems != "") {
                            $(parentContainer).addClass("height70");
                            $(textHolder).text(selectedItems);
                            $(deleteBtn).show();
                            $(".clearWholeForm").show();
                        } else {
                            if (parentContainer.hasClass("height70")) {
                                $(parentContainer).removeClass("height70");
                                $(deleteBtn).hide();
                            }
                        }
                        hideFormClearBtn();
                    });

                    $(".closeFilter .closeFilterBtn").bind("click", function () {

                        if ($("#hiddenFilter").hasClass("menuShow")) {
                            $("#hiddenFilter").removeClass("menuShow");
                            setTimeout(function () {
                                $(".filterWrapper").removeClass("overflowVisible");
                                $(".filterWrapper").addClass("zindex-1");
                            }, 1000);
                        }
                    });

                    $(".hasSubFilter").bind('click', function (e) {

                        if ($(".only-mobile").css("display") == "block") {
                            e.preventDefault();

                            var menuName = $(this).text();

                            $(".subFilterHolder").removeClass("menuShow");

                            if ($(this).parent().children(".subFilterHolder").hasClass("menuShow")) {

                                $(".subFilterHolder").removeClass("menuShow");
                            } else {

                                if (updated2 == 0) {
                                    updated2 = 1;
                                }

                                if ($(this).parent().children(".subFilterHolder").children(".subFilter").children(".subfilterLi").hasClass("subFilterBackBtnHolder")) {

                                } else {

                                    var checkboxHolder = $(this).parent().children(".subFilterHolder").attr('id');

                                    $(this).parent().children(".subFilterHolder").children(".subFilter").prepend("<li class='subfilterLi subFilterBackBtnHolder'><span class='subFilterBackBtn'>" + menuName + "</span><span class='clearFilter' onclick='uncheckAllcheckboxes(" + checkboxHolder + ")'>изчисти</span></li>");
                                }

                                $(this).parent().children(".subFilterHolder").addClass("menuShow");

                                $(".subFilterBackBtn").bind("click", function () {

                                    $(this).parent().parent().parent().removeClass("menuShow");

                                });
                            }
                        }
                    });

                    $("#mobileFilterSubmit").bind('click', function (e) {

                        e.preventDefault();

                        console.log($("#mobileFilterSubmit").closest('form').serialize());

                    });

                });

                function get_all_checkbox_values(current_id) {

                    var selector = '#' + current_id + ' .subFilter .subfilterLi label input:checked';

                    var selected = [];
                    $(selector).each(function () {
                        selected.push($(this).attr('value'));
                    });

                    return selected.toString();
                }

                function uncheckAllcheckboxes(selector) {
                    if (selector == "#hiddenFilter") {
                        $(".selectedItems").empty();
                        $(".filterLi").removeClass("height70");
                        $(".clearFilter").hide();
                    } else {
                        $(selector).find("label").removeClass("checkedLabel");
                        $(selector).parent().children(".selectedItems").empty();
                        $(selector).children(".subFilter").children(".subFilterBackBtnHolder").children(".clearFilter").hide();
                    }
                    $(selector).find("input:checkbox").prop('checked', false);
                    hideFormClearBtn();
                }

                function hideFormClearBtn() {

                    var isEmpty = 1;

                    $('.selectedItems').each(function () {
                        if ($(this).text() != "") {
                            isEmpty = isEmpty + 1;
                        }
                    });
                    console.log("isEmpty -  " + isEmpty);
                    if (isEmpty == 1) {
                        $(".clearWholeForm").hide();
                    }
                }

            </script>

            <!-- END OF SCRIPT FOR MOBILE  -->

            <footer>
                <div class="topFooter">
                    <div class="container">
                        <div class="footerRow">
                            <nav>
                                <h3 class="footerTitle">Информация</h3>
                                <ul class="footerMenu">
                                    <li><a href="">За нас</a></li>
                                    <li><a href="">Контакт</a></li>
                                    <li><a href="">Често Задавани Въпроси</a></li>
                                </ul>
                            </nav>
                            <div class="socialsContainer">
                                <h3 class="footerTitle">Последвай ни</h3>
                                <ul class="socialsRow">
                                    <li><a href=""><img src="images/icon-fb.png" alt=""></a></li>
                                    <li><a href=""><img src="images/icon-instagram.png" alt=""></a></li>
                                    <li><a href=""><img src="images/icon-twitter.png" alt=""></a></li>
                                    <li><a href=""><img src="images/icon-pinterest.png" alt=""></a></li>
                                </ul>
                            </div>
                            <div class="payments">
                                <ul class="paymentsRow">
                                    <li class="logo paypal"></li>
                                    <li class="logo mastercard"></li>
                                    <li class="logo visa"></li>
                                    <li class="logo maestro"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottomFooter">
                    <div class="container">
                        <div class="footerRow">
                            <nav>
                                <ul class="footerMenu2">
                                    <li><a href="">Условия за ползване</a></li>
                                    <li><a href="">Политика на конфиденциалност</a></li>
                                    <li><a href="">Бисквитки</a></li>
                                </ul>
                            </nav>
                            <div class="logoFooter">
                                <a class="logo" href=""><img src="images/logo_white.png" alt=""></a>
                                <span class="text">© 2019 Arthabeauty</span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
    </body>
</html>