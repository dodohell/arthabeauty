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
	<link rel="stylesheet" type="text/css" href="css/style.css">


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="js/swiper.min.js"></script>
	<script src="js/jquery.fancybox.min.js"></script>
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

	<div class="profileOverflow">

		<div class="userWelcome">
			<div class="container">
				<div class="topRow">
					<div class="initials">TC</div>
					<span class="welcomeText">Здравей, Тихомира Спасова</span>
				</div>
			</div>
		</div>


		<div class="catBackgroundGradient">

			<div class="mobileProfilePageContainer">

				<div class="profHolder">
					<ul class="profOptions">
						<li class="profLi">
							<a class="profLink hasSubProf" href="">
								<span class="icons iconContainer1"></span>
								Управление на профила
								<span class="arrowRight"></span>
							</a>
							<div class="subprofHolder">
								<ul class="subprof">
								</ul>
								<div class="profContent">
									<div class="formContainer">
										<form>
											<div class="inputRow">
												<div class="inputCol6">
													<label for="mfirstName">Име</label>
													<input id="mfirstName" type="text" name="fname" value="Тихомира" placeholder="Въведи име">
												</div>
												<div class="inputCol6">
													<label for="mlastName">Фамилия</label>
													<input id="mlastName" type="text" name="lname" value="Тихомирова" placeholder="Въведи фамилия">
												</div>
											</div>
											<div class="inputRow">
												<div class="inputCol12">
													<label for="memail">Email</label>
													<input id="memail" type="text" name="email" value="tihomira@gmail.com" placeholder="Въведи email">
												</div>
											</div>
											<div class="inputRow">
												<div class="inputCol6">
													<label for="m2curPassword">Текуща парола</label>
													<input id="m2curPassword" type="password" name="curPassword" value="Тихомира" placeholder="Въведи текуща парола">
												</div>
												<div class="inputCol6">
													<label for="m2newPassword">Нова парола</label>
													<input id="m2newPassword" type="text" name="newPassword" placeholder="Въведи нова парола">
												</div>
											</div>
											<div class="submitBtnHolder">
												<input class="submitBtn" type="submit" name="submitBtn" value="Запази">
											</div>
										</form>
									</div>
								</div>
							</div>
						</li>
						<li class="profLi">
							<a class="profLink hasSubProf" href="">
								<span class="icons iconContainer2"></span>
								Поръчки
								<span class="arrowRight"></span>
							</a>
							<div class="subprofHolder">
								<ul class="subprof">
								</ul>
								<div class="profContent">
									<div class="lastOrdersContainer">
										<ul class="myList">
											<li>
												<div class="orderRow">
													<div class="orderNumber">
														<span class="left"># Поръчка</span>
														<span class="right">#35426443</span>
													</div>
													<div class="date">
														<span class="left">Дата</span>
														<span class="right">20.10.2019</span>
													</div>
													<div class="totalPrice">
														<span class="left">Обща цена</span>
														<span class="right">152лв.</span>
													</div>
													<div class="status">
														<span class="left">Статус</span>
														<span class="right green">Изпратена</span>
													</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order1" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
											</li>
											<li>
												<div class="orderRow">
													<div class="orderNumber">
														<span class="left"># Поръчка</span>
														<span class="right">#35426443</span>
													</div>
													<div class="date">
														<span class="left">Дата</span>
														<span class="right">20.10.2019</span>
													</div>
													<div class="totalPrice">
														<span class="left">Обща цена</span>
														<span class="right">63лв.</span>
													</div>
													<div class="status">
														<span class="left">Статус</span>
														<span class="right red">Изпратена</span>
													</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order2" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
											</li>
											<li>
												<div class="orderRow">
													<div class="orderNumber">
														<span class="left"># Поръчка</span>
														<span class="right">#35426443</span>
													</div>
													<div class="date">
														<span class="left">Дата</span>
														<span class="right">20.10.2019</span>
													</div>
													<div class="totalPrice">
														<span class="left">Обща цена</span>
														<span class="right">63лв.</span>
													</div>
													<div class="status">
														<span class="left">Статус</span>
														<span class="right red">Изпратена</span>
													</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order3" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li class="profLi">
							<a class="profLink hasSubProf" href="">
								<span class="icons iconContainer3"></span>
								Адрес на доставка
								<span class="arrowRight"></span>
							</a>
							<div class="subprofHolder">
								<ul class="subprof">
								</ul>
								<div class="profContent">
									<span class="subtitle">Добави адрес</span>
									<form>
										<div class="inputRow">
											<div class="inputCol6">
												<label for="mfirstName2">Име</label>
												<input id="mfirstName2" type="text" name="fname" placeholder="Въведи име">
											</div>
											<div class="inputCol6">
												<label for="mlastName2">Фамилия</label>
												<input id="mlastName2" type="text" name="lname" placeholder="Въведи фамилия">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<label for="mphoneNumber">Телефонен номер</label>
												<input id="mphoneNumber" type="text" name="phone_number" placeholder="Въведи номер">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol8">
												<label for="maddress">Адрес</label>
												<input id="maddress" type="text" name="address" placeholder="Въведи адрес">
											</div>
											<div class="inputCol4">
												<label for="mzipCode">Пощенски код</label>
												<input id="mzipCode" type="text" name="zip_code" placeholder="Въведи пощенски код">
											</div>
										</div>
										<div class="submitBtnHolder">
											<input class="submitBtn" type="submit" name="submitBtn" value="Запази">
										</div>
									</form>
								</div>
							</div>
						</li>
						<li class="profLi">
							<a class="profLink hasSubProf" href="">
								<span class="icons iconContainer4"></span>
								Данни за плащане
								<span class="arrowRight"></span>
							</a>
							<div class="subprofHolder">
								<ul class="subprof">
								</ul>
								<div class="profContent">
									<form>
										<div class="radio-item">
											<input type="radio" id="mopt1" name="payment" value="bank transaction">
											<label for="mopt1">Банков превод</label>
										</div>

										<div class="radio-item">
											<input type="radio" id="mopt2" name="payment" value="borica system">
											<label for="mopt2">Кредитна/дебитна карта</label>
										</div>

										<div class="radio-item">
											<input type="radio" id="mopt3" name="payment" value="easypay">
											<label for="mopt3">Easypay</label>
										</div>
									</form>
								</div>
							</div>
						</li>
						<li class="profLi">
							<a class="profLink hasSubProf" href="">
								<span class="icons iconContainer5"></span>
								Email предпочитания
								<span class="arrowRight"></span>
							</a>
							<div class="subprofHolder">
								<ul class="subprof">
								</ul>
								<div class="profContent">
									<span class="subtitle2">Промоционални</span>
									<div class="grayContainerS">
										<div class="left">
											Статус: <span class="green">Абониран</span>
										</div>
										<div class="right">
											<input id="mpromoEmail" class="active" type="submit" name="promo_email" value="Прекъсни">
										</div>
									</div>
									<span class="subtitle2">Уведомителни</span>
									<div class="grayContainerS">
										<div class="left">
											Статус: <span class="gray">Неактивен</span>
										</div>
										<div class="right">
											<input id="minfoEmail" class="unactive" type="submit" name="info_email" value="Абонирай се">
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>

			</div>

			<div class="profilePageContainer">
				<div class="container">
					<div class="matTabsBox">


						<div id="holderLeft">
							<div id="material-tabs">
								<a id="tab1-tab" href="#tab1" class="active"><span class="icons iconContainer1"></span>Управление на профила</a>
								<a id="tab2-tab" href="#tab2"><span class="icons iconContainer2"></span>Поръчки</a>
								<a id="tab3-tab" href="#tab3"><span class="icons iconContainer3"></span>Адрес на доставка</a>
								<a id="tab4-tab" href="#tab4"><span class="icons iconContainer4"></span>Данни за плащане</a>
								<a id="tab5-tab" href="#tab5"><span class="icons iconContainer5"></span>Email предпочитания</a>
							</div>
						</div>

						<div id="holderRight">
							<div class="tab-content">
								<div id="tab1">
									<h3 class="title">Управление на профила</h3>
									<div class="formContainer">
										<form>
											<div class="inputRow">
												<div class="inputCol6">
													<label for="firstName">Име</label>
													<input id="firstName" type="text" name="fname" value="Тихомира" placeholder="Въведи име">
												</div>
												<div class="inputCol6">
													<label for="lastName">Фамилия</label>
													<input id="lastName" type="text" name="lname" value="Тихомирова" placeholder="Въведи фамилия">
												</div>
											</div>
											<div class="inputRow">
												<div class="inputCol12">
													<label for="email">Email</label>
													<input id="email" type="text" name="email" value="tihomira@gmail.com" placeholder="Въведи email">
												</div>
											</div>
											<div class="inputRow">
												<div class="inputCol6">
													<label for="curPassword">Текуща парола</label>
													<input id="curPassword" type="password" name="curPassword" value="Тихомира" placeholder="Въведи текуща парола">
												</div>
												<div class="inputCol6">
													<label for="newPassword">Нова парола</label>
													<input id="newPassword" type="text" name="newPassword" placeholder="Въведи нова парола">
												</div>
											</div>
											<div class="submitBtnHolder">
												<input class="submitBtn" type="submit" name="submitBtn" value="Запази">
											</div>
										</form>
									</div>
								</div>
								<div id="tab2">
									<h3 class="title">Поръчки</h3>
									<div class="lastOrdersContainer">
										<div class="orderRow headingRow">
											<div class="orderNumber"># Поръчка</div>
											<div class="date">Дата</div>
											<div class="totalPrice">Обща цена</div>
											<div class="status">Статус</div>
											<div class="action"></div>
										</div>
										<ul class="myList">
											<li>
												<div class="orderRow">
													<div class="orderNumber">#35426443</div>
													<div class="date">20.10.2019</div>
													<div class="totalPrice">152лв.</div>
													<div class="status green">Изпратена</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order1" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
												<div style="display: none;" id="order1" class="animated-modal">
													<h2 data-selectable="true">Поръчка #35426443</h2>
													<div data-selectable="true" class="fancyBoxContent">

														<ul class="products">
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod1.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>3</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod2.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod5.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod3.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
														</ul>
														<br>
														<div class="total" style="padding-right: 50px;">Доставка до: <span class="font2r">гр.София жк. Младост 5 <br> бл.100 вх. 2 ап.7</span></div>
														<hr>
														<div class="total">Общо:  <span class="font2b">324.34 лв.</span></div>
													</div>
												</div>
											</li>
											<li>
												<div class="orderRow">
													<div class="orderNumber">#35426443</div>
													<div class="date">02.10.2019</div>
													<div class="totalPrice">133лв.</div>
													<div class="status green">Изпратена</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order2" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
												<div style="display: none;" id="order2" class="animated-modal">
													<h2 data-selectable="true">Поръчка #35426443</h2>
													<div data-selectable="true" class="fancyBoxContent">

														<ul class="products">
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod1.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>3</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod2.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod5.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod3.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
														</ul>
														<br>
														<div class="total" style="padding-right: 50px;">Доставка до: <span class="font2r">гр.София жк. Младост 5 <br> бл.100 вх. 2 ап.7</span></div>
														<hr>
														<div class="total">Общо:  <span class="font2b">324.34 лв.</span></div>
													</div>
												</div>
											</li>
											<li>
												<div class="orderRow">
													<div class="orderNumber">#35364431</div>
													<div class="date">20.10.2019</div>
													<div class="totalPrice">52лв.</div>
													<div class="status red">Отказана</div>
													<div class="action">
														<a data-animation-duration="700" data-fancybox data-src="#order3" href="javascript:;" href="#">Преглед</a>
													</div>
												</div>
												<div style="display: none;" id="order3" class="animated-modal">
													<h2 data-selectable="true">Поръчка #35426443</h2>
													<div data-selectable="true" class="fancyBoxContent">

														<ul class="products">
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod1.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>3</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod2.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod5.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
															<li>
																<div class="product">
																	<div class="productImage" style="background-image: url(images/cat-prod3.jpg);"></div>
																	<div class="productInfo">
																		<div class="prodName">Diesel Only The Brave 50ml</div>
																		<div class="quantity">
																			Количество: <strong>1</strong>
																		</div>
																		<span class="prodPrice">80 лв.</span>
																		<div class="actionsRow">
																			<a class="add2FavoritesList" href=""><img src="images/hearth-green-btn.svg" alt=""></a>
																			<a class="showProduct" href="">Разгледай</a>
																		</div>
																	</div>
																</div>
															</li>
														</ul>
														<br>
														<div class="total">Общо:  <span class="font2b">324.34 лв.</span></div>
													</div>
												</div>
											</li>
										</ul>

									</div>
								</div>
								<div id="tab3">
									<h3 class="title">Адрес на доставка</h3>
									<span class="subtitle">Добави адрес</span>
									<form>
										<div class="inputRow">
											<div class="inputCol6">
												<label for="firstName2">Име</label>
												<input id="firstName2" type="text" name="fname" placeholder="Въведи име">
											</div>
											<div class="inputCol6">
												<label for="lastName2">Фамилия</label>
												<input id="lastName2" type="text" name="lname" placeholder="Въведи фамилия">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<label for="phoneNumber">Телефонен номер</label>
												<input id="phoneNumber" type="text" name="phone_number" placeholder="Въведи номер">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol8">
												<label for="address">Адрес</label>
												<input id="address" type="text" name="address" placeholder="Въведи адрес">
											</div>
											<div class="inputCol4">
												<label for="zipCode">Пощенски код</label>
												<input id="zipCode" type="text" name="zip_code" placeholder="Въведи пощенски код">
											</div>
										</div>
										<div class="submitBtnHolder">
											<input class="submitBtn" type="submit" name="submitBtn" value="Запази">
										</div>
									</form>
								</div>
								<div id="tab4">
									<h3 class="title">Данни за плащане</h3>
									<form>
										<div class="radio-item">
											<input type="radio" id="opt1" name="payment" value="bank transaction">
											<label for="opt1">Банков превод</label>
										</div>

										<div class="radio-item">
											<input type="radio" id="opt2" name="payment" value="borica system">
											<label for="opt2">Кредитна/дебитна карта</label>
										</div>

										<div class="radio-item">
											<input type="radio" id="opt3" name="payment" value="easypay">
											<label for="opt3">Easypay</label>
										</div>
									</form>
								</div>
								<div id="tab5">
									<h3 class="title">Email предпочитания</h3>
									<span class="subtitle2">Промоционални</span>
									<div class="grayContainerS">
										<div class="left">
											Статус: <span class="green">Абониран</span>
										</div>
										<div class="right">
											<input id="promoEmail" class="active" type="submit" name="promo_email" value="Прекъсни">
										</div>
									</div>

									<span class="subtitle2">Уведомителни</span>
									<div class="grayContainerS">
										<div class="left">
											Статус: <span class="gray">Неактивен</span>
										</div>
										<div class="right">
											<input id="infoEmail" class="unactive" type="submit" name="info_email" value="Абонирай се">
										</div>
									</div>
								</div>
							</div>
						</div>


					</div>
				</div>
			</div>


		</div>
	</div>


	<script>

		var updated = 0;
		var updated2 = 0;

		$( document ).ready(function() {

			$("#menuToggle").bind("click", function(){

				if($(this).children("input").is(":checked")){		
					if(updated==0){
						updated = 1;
					}
					$(".bottomLine .container .headRow").css("height", "0px");
					$("#searchIcon").removeClass("searchIconActive");
					$(".searchHolder").removeClass("menuShow");
					$("nav.headRow").addClass("overflowVisible");
					$(".topLine").addClass("overflowVisible2");
					$(".topLine .container .headRow").addClass("menuShow");
					$(".menuHolder").addClass("menuShow");
				}else{

					$("nav.headRow").removeClass("overflowVisible");
					$(".topLine").removeClass("overflowVisible2");
					$(".topLine .container .headRow").removeClass("menuShow");
					$(".menuHolder").removeClass("menuShow");
				} 
			});

			$("#searchIcon").bind('click',function(e){
				if ($(".searchHolder").hasClass("menuShow")) {
					$(".bottomLine .container .headRow").css("height", "0px");
					$("#searchIcon").removeClass("searchIconActive");
					$(".searchHolder").removeClass("menuShow");
				}else{
					if($("#menuToggle").children("input").is(":checked")){
						$("#menuToggle").find("input").trigger("click");
					}
					$("#searchIcon").addClass("searchIconActive");
					$(".bottomLine .container .headRow").css("height", "64px");
					$(".searchHolder").addClass("menuShow");
				}

			});

			$(".hasSubMenu").bind('click',function(e){

				if ($(".only-mobile").css("display") == "block") {
					e.preventDefault();

					var menuName = $(this).text();

					$(".submenuHolder").removeClass("menuShow");

					if($(this).parent().children(".submenuHolder").hasClass("menuShow")){		

						$(".submenuHolder").removeClass("menuShow");
					}else{

						if(updated2==0){
							updated2 = 1;
						}

						if ($(this).parent().children(".submenuHolder").children(".submenu").children(".submenuLi").hasClass("subMenubBackBtn")) {
						}else {

							$(this).parent().children(".submenuHolder").children(".submenu").prepend("<li class='submenuLi subMenubBackBtn'><span>"+menuName+"</span> </li>" );
						}

						$(this).parent().children(".submenuHolder").addClass("menuShow");

						$(".subMenubBackBtn").bind("click", function(){

							$(this).parent().parent().removeClass("menuShow");

						});
					} 
				}
			});


			$(".hasSubProf").bind('click',function(e){

				if ($(".only-mobile").css("display") == "block") {
					e.preventDefault();

					var menuName = $(this).text();

					$(".subprofHolder").removeClass("menuShow");

					if($(this).parent().children(".subprofHolder").hasClass("menuShow")){		

						$(".subprofHolder").removeClass("menuShow");
					}else{

						if(updated2==0){
							updated2 = 1;
						}

						if ($(this).parent().children(".subprofHolder").children(".subprof").children(".subprofLi").hasClass("subProfBackBtn")) {
						}else {

							$(this).parent().children(".subprofHolder").children(".subprof").prepend("<li class='subprofLi subProfBackBtn'><span>"+menuName+"</span> </li>" );
						}

						$(this).parent().children(".subprofHolder").addClass("menuShow");

						$(".subProfBackBtn").bind("click", function(){

							$(this).parent().parent().removeClass("menuShow");

						});
					} 
				}
			});


			$('#material-tabs').each(function() {

				var $active, $content, $links = $(this).find('a');

				$active = $($links[0]);
				$active.addClass('active');

				$content = $($active[0].hash);

				$links.not($active).each(function() {
					$(this.hash).hide();
				});

				$(this).on('click', 'a', function(e) {

					$active.removeClass('active');
					$content.hide();

					$active = $(this);
					$content = $(this.hash);

					$active.addClass('active');
					$content.show();

					e.preventDefault();
				});
			});


			var swiper = new Swiper('#swiper-container1', {
				slidesPerView: 1,
				loop: true,
				autoplay: {
					delay: 2500,
				},
			});

		});

	</script>


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