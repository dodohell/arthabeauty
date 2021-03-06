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
	
	
	<div class="optionsContainer">
		<div class="container">
			<div class="optionsRow">
				<div class="option">
					<div class="iconHolder"><img src="images/icon-truck.png" alt=""></div>
					<span class="optionText">?????????????????? ????????????????</span>
				</div>
				<div class="option">
					<div class="iconHolder"><img src="images/icon-star.png" alt=""></div>
					<span class="optionText">?????????????????????????????? ????????????????</span>
				</div>
				<div class="option">
					<div class="iconHolder"><img src="images/icon-shield.png" alt=""></div>
					<span class="optionText">???????????????? ???????????? ????????????????????</span>
				</div>
			</div> 
		</div>
	</div>


	<div class="breadcrumbsContainer">
		<div class="container">
			<div class="breadcrubsRow">
				<a href="">????????????</a>
				<span>|</span>
				<a href="">?????????? ???????????????? ??????????????</a> 
			</div>
		</div>
	</div>

	


	<div class="catBackgroundGradient">


		<div class="contactsContainer">
			<div class="container">
				<h1 class="pageTitle faqTitle">?????????? ???????????????? ??????????????</h1>
				
				<div class="faqContainer">
					<button class="collapsible">Lorem ipsum dolor sit amet</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 1</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 2</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 3</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 1</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 2</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
					<button class="collapsible">Lorem ipsum dolor sit amet 3</button>
					<div class="content">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>
				</div>
			</div>
		</div>

	</div>

	

	<script>
		var coll = document.getElementsByClassName("collapsible");
		var i;

		for (i = 0; i < coll.length; i++) {
			coll[i].addEventListener("click", function() {
				this.classList.toggle("active");
				var content = this.nextElementSibling;
				if (content.style.maxHeight){
					content.style.maxHeight = null;
					content.style.marginBottom = null;
				} else {
					content.style.maxHeight = content.scrollHeight + "px";
					content.style.marginBottom = 8+"px";
				} 
			});
		}
	</script>

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