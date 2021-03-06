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
	<link rel="stylesheet" type="text/css" href="css/slick.css">
	<link rel="stylesheet" type="text/css" href="css/slick-theme.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="js/swiper.min.js"></script>
	<script src="js/jquery.fancybox.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="js/select2.min.js"></script>
	<script src="js/slick.min.js"></script>

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
								<a data-animation-duration="700" data-fancybox data-src="#loginForm" href="javascript:;">????????</a><span>??????</span><a data-animation-duration="700" data-fancybox data-src="#signUpForm" href="javascript:;">??????????????????????</a>
							</div>
							<div class="loggedIn"><a href="#"><div class="imgContainer"><img src="images/icon-profile.svg" alt=""></div>????????????</a></div>

							<div style="display: none;" id="signUpForm" class="animated-modal">
								<h2 data-selectable="true">??????????????????????</h2>
								<div data-selectable="true" class="fancyBoxContent">
									<a class="facebookLogin" href="#">?????????????????????? ?? facebook</a>
									<a class="googleLogin" href="#">?????????????????????? ?? Google</a>
									<div class="orSeparator">
										<span class="text">??????</span>
										<span class="line"></span>
									</div>
									<form>
										<div class="inputRow">
											<div class="inputCol6">
												<input type="text" name="fname" placeholder="????e">
											</div>
											<div class="inputCol6">
												<input type="text" name="lname" placeholder="??????????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="text" name="email" placeholder="??????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="text" name="password" placeholder="????????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<label>
													<input type="checkbox" name="subscribe">
													<span>????, ?????????? ???? ???? ???????????????? ???? ?????????????????????????? ???????????? ???? Arthabeauty</span>
												</label>
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="submit" class="submitBtn" value="?????????????????????? ????">
											</div>
										</div>
									</form>
									<div class="loginBox">
										???????? ????????????? <a class="loginBtn" href="#">????????</a>
									</div>
									<div class="termsBox">
										<a href="#">???????????????? ???? ????????????????????????????????</a>
										<a href="#">?????????????? ???? ????????????????</a>
									</div>
								</div>
							</div>

							<div style="display: none;" id="loginForm" class="animated-modal">
								<h2 data-selectable="true">????????</h2>
								<div data-selectable="true" class="fancyBoxContent">
									<a class="facebookLogin" href="#">???????????????? ?? facebook</a>
									<a class="googleLogin" href="#">???????????????? ?? Google</a>
									<div class="orSeparator">
										<span class="text">??????</span>
										<span class="line"></span>
									</div>
									<form>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="text" name="email" placeholder="??????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="text" name="password" placeholder="????????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<div class="forgotPass">
													<a data-animation-duration="700" data-fancybox data-src="#forgotPassForm" href="javascript:;" href="#">?????????????????? ?????????????</a>
												</div>
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="submit" class="submitBtn" value="?????????????????????? ????">
											</div>
										</div>
									</form>
									<div class="loginBox">
										?????????? ????????????? <a class="loginBtn" href="#">?????????????????????? ????</a>
									</div>
									<div class="termsBox">
										<a href="#">???????????????? ???? ????????????????????????????????</a>
										<a href="#">?????????????? ???? ????????????????</a>
									</div>
								</div>
							</div>

							<div style="display: none;" id="forgotPassForm" class="animated-modal">
								<h2 data-selectable="true">?????????????????? ????????????</h2>
								<div data-selectable="true" class="fancyBoxContent">
									<div class="instruction">???????? ???????????????? ???????? email.</div>
									<form>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="text" name="email" placeholder="??????????">
											</div>
										</div>
										<div class="inputRow">
											<div class="inputCol12">
												<input type="submit" class="submitBtn" value="?????????? ????????????">
											</div>
										</div>
									</form>
									<div class="termsBox">
										<a href="#">???????????????? ???? ????????????????????????????????</a>
										<a href="#">?????????????? ???? ????????????????</a>
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
											<span class="headTitle">??????????????</span>
											<span class="headText">5 ??????????????a</span>
										</div>
										<ul class="products">
											<li>
												<div class="productImage">
													<div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
												</div>
												<div class="productInfo">
													<span class="name">?????????????? ?????????? ?????? ?????????????? ?????????? ?????? ?????????????? ?????????? ??????</span>
													<span class="excerpt">???????? ???????????????? ???????? ???????????????? ???????? ????????????????</span>
												</div>
												<div class="productPrice">
													99.99 ???? 
												</div>
											</li>
											<li>
												<div class="productImage">
													<div class="image" style="background-image: url(images/top-cat-1.jpg);"></div>
												</div>
												<div class="productInfo">
													<span class="name">?????????????? ?????????? ??????</span>
													<span class="excerpt">???????? ???????????????? ???????? ???????????????? ???????? ????????????????</span>
												</div>
												<div class="productPrice">
													3 x 242.99 ???? 
												</div>
											</li>
											<li>
												<div class="productImage">
													<div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
												</div>
												<div class="productInfo">
													<span class="name">?????????????? ?????????? ?????? ?????????????? ?????????? ?????? ?????????????? ?????????? ??????</span>
													<span class="excerpt">???????? ???????????????? ???????? ???????????????? ???????? ????????????????</span>
												</div>
												<div class="productPrice">
													99.99 ???? 
												</div>
											</li>
											<li>
												<div class="productImage">
													<div class="image" style="background-image: url(images/top-cat-3.jpg);"></div>
												</div>
												<div class="productInfo">
													<span class="name">?????????????? ?????????? ?????? ?????????????? ?????????? ?????? ?????????????? ?????????? ??????</span>
													<span class="excerpt">???????? ???????????????? ???????? ???????????????? ???????? ????????????????</span>
												</div>
												<div class="productPrice">
													99.99 ???? 
												</div>
											</li>
										</ul>
										<div class="cartFooter">
											<span class="total">????????:<strong>198.99 ????</strong></span>
											<span class="checkout">??????????</span>
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
								<a class="menuLink" href=""><span class="imgContainer"><img src="images/icon-hearth.png" alt=""></span>????????????</a>
							</li>
							<li class="menuLi">
								<a class="menuLink hasSubMenu" href="">????????</a>
								<div class="submenuHolder">
									<ul class="submenu">
										<li class="submenuLi"><a href="">??????????????0</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">???????????? ?? ??????</a></li>
										<li class="submenuLi"><a href="">???????? ?? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
									</ul>
								</div>
							</li>
							<li class="menuLi">
								<a class="menuLink hasSubMenu" href="">????????</a>
								<div class="submenuHolder">
									<ul class="submenu">
										<li class="submenuLi"><a href="">??????????????9</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">???????????? ?? ??????</a></li>
										<li class="submenuLi"><a href="">???????? ?? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
									</ul>
								</div>
							</li>
							<li class="menuLi">
								<a class="menuLink hasSubMenu" href="">????????</a>
								<div class="submenuHolder">
									<ul class="submenu">
										<li class="submenuLi"><a href="">??????????????7</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">???????????? ?? ??????</a></li>
										<li class="submenuLi"><a href="">???????? ?? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
									</ul>
								</div>
							</li>
							<li class="menuLi">
								<a class="menuLink hasSubMenu" href="">????????????????</a>
								<div class="submenuHolder">
									<ul class="submenu">
										<li class="submenuLi"><a href="">??????????????1</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
										<li class="submenuLi"><a href="">???????????? ?? ??????</a></li>
										<li class="submenuLi"><a href="">???????? ?? ????????</a></li>
										<li class="submenuLi"><a href="">?????????? ???? ????????</a></li>
									</ul>
								</div>
							</li>
							<li class="menuLi">
								<a class="menuLink" href="">????????????????</a>
							</li>
							<li class="menuLi">
								<a class="menuLink" href="">????????</a>
							</li>
							<li class="menuLi">
								<a class="menuLink" href="">ArthaBeauty Consultant</a>
							</li>
						</ul>
					</div>
					<form class="searchHolder" action="">
						<input type="text" name="searchInput" placeholder="??????????...">
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
				<a href="">????????????</a>
				<span>|</span>
				<a href="">??????????????</a> 
				<span>|</span>
				<a href="">??????????????</a> 
			</div>
		</div>
	</div>

	<div class="catBackgroundGradient">

		<div class="singleProduct">
			<div class="container">
				<div class="mobileHeaderRow">
					<div class="left">
						<h2 class="heading">Diesel Only The Brave 50 ml</h2>
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
								<span class="oldPrice">180 ????.</span>
								<span class="currentPrice">80 ????.</span>
							</div>
							<div class="deliveryInfo">???????????????? ????: 14.08.2019</div>
						</div>
						<div class="codeBox">
							<span>??????: 200106</span>
							<span class="inStock">
								<span class="imgContainer"><img src="images/check-green.svg" alt=""></span>
								?? ??????????????????
							</span>
						</div>
					</div>
					<div class="excerpt"> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</div>
					<div class="iconsList">
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/truck-gray.svg" alt=""></span>
							?????????????????? ????????????????
						</span>
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/star-gray-e.svg" alt=""></span>
							?????????????????????????????? ????????????????
						</span>
						<span class="singleIcon">
							<span class="imgContainer"><img src="images/shield-gray-e.svg" alt=""></span>
							???????????????? ???????????? ????????????????????
						</span>
					</div>
					<form>
						<div class="selectsRow">
							<div class="select1">
								????????????????????:
								<select class="prodSize">
									<option>50????.</option>
									<option>150????.</option>
									<option>250????.</option>
									<option>350????.</option>
								</select>
							</div>
							<div class="select2">
								????????????????????:
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
							<button><img src="images/add-to-cart-icon.svg" alt="">???????????? ?? ??????????????</button>
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
					<div class="headering">????????????????????</div>
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
					<div class="headering">????????????????????</div>
					<div class="info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
				</div>
				<div class="right">
					<div class="headering">????????????????</div>
					<div class="info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
				</div>
			</div>
		</div>
	</div>

	<div class="reviewContainer">
		<div class="container">
			<h2 class="heading">????????????</h2>
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
					<span class="rating">5.0 ???? 5 ???????????? / 429 ????????????</span>
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
					<span class="text">100% ???????? <br> ??????????????????????</span>
				</div>
			</div>
		</div>
	</div>


	<div class="commentsContainer">
		<div class="container">
			<div class="allComents">
				<div class="commentRow">
					<div class="left">
						<span class="initials">????</span>
						<span class="fullName">???????? ????????????????</span>
					</div>
					<div class="right">
						<span class="title">??????????!</span>
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
						<span class="initials">????</span>
						<span class="fullName">???????????? ????????????</span>
					</div>
					<div class="right">
						<span class="title">?????????? ?????? ??????????????!</span>
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
					<button class="showMoreReviews">?????? ?????? ???????????? (10)</button>
					<button data-animation-duration="700" data-fancybox data-src="#addCommentForm" href="javascript:;" class="addCommentBtn">???????????? ????????</button>
				</div>
			</div>
		</div>
	</div>

	<div style="display: none;" id="addCommentForm" class="animated-modal">
		<h2 data-selectable="true">???????????? ????????</h2>
		<div data-selectable="true" class="fancyBoxContent">
			<form>
				<div class="inputRow">
					<div class="inputCol12 reviewTitleHolder">
						<input type="text" name="reviewTitle" placeholder="????????????????">
					</div>
				</div>
				<div class="inputRow">
					<div class="inputCol12">
						<span class="rateText">??????????:</span>
					</div>
				</div>
				<div class="inputRow">
					<div class="inputCol12">
						<div class="rate">
							<input type="radio" id="star5" name="rate" value="5" />
							<label for="star5" title="text"></label>
							<input type="radio" id="star4" name="rate" value="4" />
							<label for="star4" title="text"></label>
							<input type="radio" id="star3" name="rate" value="3" />
							<label for="star3" title="text"></label>
							<input type="radio" id="star2" name="rate" value="2" />
							<label for="star2" title="text"></label>
							<input type="radio" id="star1" name="rate" value="1" />
							<label for="star1" title="text"></label>
						</div>
					</div>
				</div>
				<div class="inputRow">
					<div class="inputCol12">
						<textarea class="reviewContent" name="comment" placeholder="???????????? ????????"></textarea>
					</div>
				</div>
				<div class="inputRow">
					<div class="inputCol12">
						<input type="submit" class="submitBtn" value="????????????????????">
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="suggestedProductsContainer">
		<div class="container">
			<h2 class="title">???????????????????????????? ????????????????</h2>
			<div class="slider">
				<div id="slickSlider1" class="productsRow">
					<div class="swiper-slide singleItem">
						<a class="product" href="">
							<div class="imgContainer" style="background-image: url(images/home-prod-2.jpg);"></div>
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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
							<h3 class="name">??????????????</h3>
							<span class="excerpt">???????? ????????????????</span>
							<span class="price">80.00????</span>
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


	<div class="bannerTop bannerTopinFooter">
		<div class="container">
			<div class="backgroundImage" style="background-image: url(images/cat-banner.jpg);">
				<h2 class="heading">???????????????? ???? <span class="stronger">???????????? ?????????? ??????????????!</span></h2>
				<a class="goTo" href="">?????????????????? ????????</a>
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


			$('#slickSlider1').slick({
				slidesToShow: 8,
				autoplay: true,
				autoplaySpeed: 2000,
				infinite: true,
				speed: 2000,
				centerMode: true,
				slidesToScroll: 8,
				responsive: [
				{
					breakpoint: 1780,
					settings: {
						slidesToShow: 7,
						slidesToScroll: 7,
					}
				},
				{
					breakpoint: 1600,
					settings: {
						slidesToShow: 6,
						slidesToScroll: 6,
					}
				},
				{
					breakpoint: 1400,
					settings: {
						slidesToShow: 5,
						slidesToScroll: 5,
					}
				},
				{
					breakpoint: 1200,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 4,
					}
				},
				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
					}
				},
				{
					breakpoint: 690,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				}
				]
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

				$(".showMoreReviews").bind("click", function(){

					$(".commentRow").slideDown({
						start: function () {
							$(this).css({
								display: "flex"
							})
						}
					});
					$(".showMoreReviews").hide();
				});



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
					slidesPerView: 7,
					spaceBetween: 12,
					loop: true,
					breakpoints: {
						640: {
							slidesPerView: 3,
							spaceBetween: 8,
						},
						768: {
							slidesPerView: 4,
							spaceBetween: 8,
						},
						1024: {
							slidesPerView: 7,
							spaceBetween: 12,
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
							<h3 class="footerTitle">????????????????????</h3>
							<ul class="footerMenu">
								<li><a href="">???? ??????</a></li>
								<li><a href="">??????????????</a></li>
								<li><a href="">?????????? ???????????????? ??????????????</a></li>
							</ul>
						</nav>
						<div class="socialsContainer">
							<h3 class="footerTitle">?????????????????? ????</h3>
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
								<li><a href="">?????????????? ???? ????????????????</a></li>
								<li><a href="">???????????????? ???? ????????????????????????????????</a></li>
								<li><a href="">??????????????????</a></li>
							</ul>
						</nav>
						<div class="logoFooter">
							<a class="logo" href=""><img src="images/logo_white.png" alt=""></a>
							<span class="text">?? 2019 Arthabeauty</span>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</body>
	</html>