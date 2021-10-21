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


	<article>

		<div class="catBackgroundGradient">

			<div class="articleTopContainer">
				<div class="container">
					<h1 class="title">Главна Статия</h1>
					<div class="authorInfo">
						<div class="userPic">
							<img src="images/user2.png">
						</div>
						<div class="userInfo">
							<span class="name">Име Фамилия</span>
							<span class="date">21 Август 2 мин. четене</span>
						</div>
					</div>
					<div class="articleWrapper">
						<div class="imgContainer" style="background-image: url(images/main-article.jpg);"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="grayContainer5">
			<div class="container">
				<div class="articleContent1">	
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
					<br>
					<br>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
				</div>
			</div>
		</div>
	</article>

	<div class="grayBackgroundContainer" style="background-color: #E7F3F0">

		<div class="twoArticlesOnRow">
			<div class="container">
				<h3 class="title">Прочети още</h3>
				<div class="articleRow">
					<article>
						<a href="#">					
							<section class="imgContainer" style="background-image: url(images/article-11.jpg);">
								<span class="categoryType2">Категория</span>
							</section>
							<section class="content">
								<h3 class="articleTitle">Заглавие на статия</h3>
								<div class="excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sollicitudin morbi fringilla placerat consectetur adipiscing elit. Sollicitudin morbi fringilla placerat consectetur adipiscing elit. Sollicitudin morbi fringilla placerat</div>
								<div class="authorInfo">
									<div class="userPic">
										<img src="images/user2.png">
									</div>
									<div class="userInfo">
										<span class="name">Име Фамилия</span>
										<span class="date">21 Август 2 мин. четене</span>
									</div>
								</div>
							</section>
						</a>
					</article>
					<article>
						<a href="#">					
							<section class="imgContainer" style="background-image:url(images/article-22.jpg)">
								<span class="categoryType1">Категория</span>
							</section>
							<section class="content">
								<h3 class="articleTitle">Заглавие на статия</h3>
								<div class="excerpt">Lorem ipsum dolor consectetur adipiscing elit. Sollicitudin morbi fringilla placerat sit amet, consectetur adipiscing elit. Sollicitudin morbi fringilla placerat</div>
								<div class="authorInfo">
									<div class="userPic">
										<img src="images/user3.png">
									</div>
									<div class="userInfo">
										<span class="name">Име Фамилия</span>
										<span class="date">21 Август 2 мин. четене</span>
									</div>
								</div>
							</section>
						</a>
					</article>
				</div>
			</div>
		</div>

	</div>

	<div class="subscribeContainer">
		<div class="container">
			<h2 class="title">Абонирай се</h2>
			<span class="subtitle">Искаш да бъдеш уведомен/а за ексклузивни промоции и намаления? Въведи своя email и бъди в крак с най-добрите оферти!</span>
			<form>
				<input class="customInput" type="text" name="email" placeholder="Имейл адрес">
				<input class="submitBtn" type="submit" name="" value="Абонирай се">
			</form>
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