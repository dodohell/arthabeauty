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
				<span>|</span>
				<a href="">Продукт</a> 
			</div>
		</div>
	</div>

	<div class="catBackgroundGradient">

		<div class="singleProduct">
			<div class="container">
				<span class="productBrandLogo">
					<img src="images/logo-diesel.png" alt="">
				</span>
				<div class="carouselSlider">
					
					<span class="likedBox likedBoxRed"></span>

					<div class="swiper-container gallery-top">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<a data-fancybox="gallery" href="images/cat-prod1.jpg">
										<div class="productImage" style="background-image: url(images/cat-prod1.jpg);"></div>
									</a>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<a data-fancybox="gallery" href="images/cat-prod2.jpg">
										<div class="productImage" style="background-image: url(images/cat-prod2.jpg);"></div>
									</a>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<a data-fancybox="gallery" href="images/cat-prod3.jpg">
										<div class="productImage" style="background-image: url(images/cat-prod3.jpg);"></div>
									</a>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<a data-fancybox="gallery" href="images/cat-prod4.jpg">
										<div class="productImage" style="background-image: url(images/cat-prod4.jpg);"></div>
									</a>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<a data-fancybox="gallery" href="images/cat-prod5.jpg">
										<div class="productImage" style="background-image: url(images/cat-prod5.jpg);"></div>
									</a>
								</div>
							</div>
						</div>

						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
					</div>
					<div class="swiper-container gallery-thumbs">
						<div class="swiper-wrapper">

							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<div class="productImage" style="background-image: url(images/cat-prod1.jpg);">

									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<div class="productImage" style="background-image: url(images/cat-prod2.jpg);">

									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<div class="productImage" style="background-image: url(images/cat-prod3.jpg);">

									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<div class="productImage" style="background-image: url(images/cat-prod4.jpg);">

									</div>
								</div>
							</div>
							<div class="swiper-slide">
								<div class="swiper-slide-container">
									<div class="productImage" style="background-image: url(images/cat-prod5.jpg);">

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="productInfo">
					<div class="headerRow">
						<div class="left">
							<h1 class="heading">Diesel Only The Brave 50 ml</h1>
							<span class="subtitle">Eau de toillete spray</span>
						</div>
						<div class="right">
							<div class="starsRow">
								<span class="starBox"><img src="images/star.svg" alt=""></span>
								<span class="starBox"><img src="images/star.svg" alt=""></span>
								<span class="starBox"><img src="images/star.svg" alt=""></span>
								<span class="starBox"><img src="images/star.svg" alt=""></span>
								<span class="starBox"><img src="images/star.svg" alt=""></span>
							</div>
							<span class="counter">(10)</span>
						</div>
					</div>
					<div class="priceRow">
						<div class="priceCol">
							<div class="priceBox">
								<span class="oldPrice">180 лв.</span>
								<span class="currentPrice">80 лв.</span>
							</div>
							<div class="deliveryInfo">Доставка до: 14.08.2019</div>
						</div>
						<div class="codeBox">
							<span>СЕП: 200106</span>
							<span class="inStock">
								<span class="imgContainer"><img src="images/check-green.svg" alt=""></span>
								В наличност
							</span>
						</div>
					</div>
					<div class="excerpt"> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</div>
					<div class="iconsList">
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/truck-gray.svg" alt=""></span>
							Безплатна Доставка
						</span>
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/star-gray-e.svg" alt=""></span>
							Първокачествени продукти
						</span>
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/shield-gray-e.svg" alt=""></span>
							Защитено онлайн пазаруване
						</span>
					</div>
					<form>
						<div class="selectsRow">
							<div class="select1">
								Разфасовка:
								<select class="prodSize">
									<option>50мл.</option>
									<option>150мл.</option>
									<option>250мл.</option>
									<option>350мл.</option>
								</select>
							</div>
							<div class="select2">
								Количество:
								<select class="prodQuantity">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>5</option>
									<option>6</option>
								</select>
							</div>
						</div>
						<div class="submitBtnHolder">
							<button><img src="images/add-to-cart-icon.svg" alt="">Добави в количка</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="moreInfoContainer">
		<div class="container">
			<div class="topRow">
				<div class="left">
					<div class="headering">Информация</div>
					<div class="info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
				</div>
				<div class="right">
					<div class="videoBox">
						<span class="playVideoBtn"><img id="playBtn" src="images/icon-play.svg" alt=""></span>
						<iframe src="https://www.youtube.com/embed/j0om19AI4Qs?rel=0&controls=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
			</div>
			<div class="bottomRow">
				<div class="left">
					<div class="headering">Използване</div>
					<div class="info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
				</div>
				<div class="right">
					<div class="headering">Съставки</div>
					<div class="info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
				</div>
			</div>
		</div>
	</div>

	<div class="reviewContainer">
		<div class="container">
			<h2 class="heading">Ревюта</h2>
			<div class="brandLogo"><img src="images/logo-diesel.png" alt=""></div>
			<div class="reviewRow">
				<div class="col1">
					<h3 class="brand">Diesel Only The Brave</h3>
					<span class="subTitle">Eau de toillete spray</span>
					<div class="starsRow">
						<span class="starBox"><img src="images/star.svg" alt=""></span>
						<span class="starBox"><img src="images/star.svg" alt=""></span>
						<span class="starBox"><img src="images/star.svg" alt=""></span>
						<span class="starBox"><img src="images/star.svg" alt=""></span>
						<span class="starBox"><img src="images/star.svg" alt=""></span>
					</div>
					<span class="rating">5.0 от 5 звезди / 429 ревюта</span>
				</div>
				<div class="col2">
					<div class="row1">
						<div class="stars">
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
						</div>
						<div class="progressBarContainer">
							<div class="progress">
								<div class="progress-bar" style="width: 85%;"></div>
							</div>
						</div>
					</div>
					<div class="row2">
						<div class="stars">
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
						</div>
						<div class="progressBarContainer">
							<div class="progress">
								<div class="progress-bar" style="width: 60%;"></div>
							</div>
						</div>
					</div>
					<div class="row3">
						<div class="stars">
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
						</div>
						<div class="progressBarContainer">
							<div class="progress">
								<div class="progress-bar" style="width: 50%;"></div>
							</div>
						</div>
					</div>
					<div class="row4">
						<div class="stars">
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
						</div>
						<div class="progressBarContainer">
							<div class="progress">
								<div class="progress-bar" style="width: 25%;"></div>
							</div>
						</div>
					</div>
					<div class="row5">
						<div class="stars">
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-gray.svg" alt=""></span>
							<span class="starBox"><img src="images/star-green.svg" alt=""></span>
						</div>
						<div class="progressBarContainer">
							<div class="progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col3">
					<span class="iconContainer"><img src="images/finger-up.svg" alt=""></span>
					<span class="text">100% биха <br> препоръчали</span>
				</div>
			</div>
		</div>
	</div>


	<div class="commentsContainer">
		<div class="container">
			<div class="allComents">
				<div class="commentRow">
					<div class="left">
						<span class="initials">ИГ</span>
						<span class="fullName">Иван Георгиев</span>
					</div>
					<div class="right">
						<span class="title">Супер!</span>
						<span class="date">11.09.2019</span>
						<div class="starsRow">
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
						</div>
						<div class="commentText">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
						</div>
					</div>
				</div>
				<div class="commentRow">
					<div class="left">
						<span class="initials">ГИ</span>
						<span class="fullName">Георги Иванов</span>
					</div>
					<div class="right">
						<span class="title">Много съм доволен!</span>
						<span class="date">11.09.2019</span>
						<div class="starsRow">
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
							<span class="starBox"><img src="images/star.svg" alt=""></span>
						</div>
						<div class="commentText">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
						</div>
					</div>
				</div>
				<div class="addCommentContainer">
					<button class="addCommentBtn">Напиши ревю</button>
				</div>
			</div>
		</div>
	</div>


	<div class="suggestedProductsContainer">
		<div class="container">
			<h2 class="title">Комплементарни продукти</h2>
			<div class="slider">

				<div id="swiper-container3" class="swiper-container">

					<div class="swiper-wrapper productsRow">
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-2.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-3.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-4.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-5.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-6.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-1.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-2.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-3.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-4.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-5.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-6.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
						<div class="swiper-slide singleItem">
							<a class="product" href="">
								<div class="imgContainer" style="background-image: url(images/home-prod-1.jpg);"></div>
								<h3 class="name">Продукт</h3>
								<span class="excerpt">Късо Описание</span>
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
				</div>

			</div>
		</div>
	</div>


	<div class="bannerTop bannerTopinFooter">
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







	<script>

		function addCommas(nStr){
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


		$( document ).ready(function() {

			$("#slider-range").slider({
				range: true, 
				min: 0,
				max: 300,
				step: 1,
				slide: function( event, ui ) {
					$( "#min-price").html(addCommas(ui.values[ 0 ]));

					suffix = '';
					if (ui.values[ 1 ] == $( "#max-price").data('max') ){
						suffix = ' +';
					}
					$( "#max-price").html(addCommas(ui.values[ 1 ] + suffix));         
				}
			});

			$('#playBtn').on('click', function(ev) {

				$(this).parent().next("iframe")[0].src += "&autoplay=1";
				$(this).parent().hide();
				ev.preventDefault();

			});



			var galleryTop = new Swiper('.gallery-top', {
				spaceBetween: 10,
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				loop: true,
				loopedSlides: 3
			});
			var galleryThumbs = new Swiper('.gallery-thumbs', {
				spaceBetween: 10,
				centeredSlides: true,
				slidesPerView: 'auto',
				touchRatio: 0.2,
				slideToClickedSlide: true,
				loop: true,
				loopedSlides: 3
			});

			galleryTop.controller.control = galleryThumbs;
			galleryThumbs.controller.control = galleryTop;

			$.fancybox.defaults.backFocus = false;

		});

	</script>



		<!--=====================================
		=             Footer Section            =
		======================================-->



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

				var swiper1 = new Swiper('#swiper-container1', {
					slidesPerView: 1,
					loop: true,
					autoplay: {
						delay: 2500,
					},
				});


				var swiper3 = new Swiper('#swiper-container3', {
					slidesPerView: 1,
					spaceBetween: 10, 
					loop: true,
					loopedSlides: 1,
					breakpoints: {
						640: {
							slidesPerView: 2,
							spaceBetween: 20,
						},
						768: {
							slidesPerView: 4,
							spaceBetween: 40,
						},
						1024: {
							slidesPerView: 7,
							spaceBetween: 12,
							centeredSlides: true,
							loopedSlides: 7,
						},
					}
				});


				$('.sortBy').select2({
					minimumResultsForSearch: -1,
				});

				$('.prodSize').select2({
					minimumResultsForSearch: -1,
				});

				$('.prodQuantity').select2({
					minimumResultsForSearch: -1,
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