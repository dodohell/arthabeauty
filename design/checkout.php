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
		<div class="container">
			<nav class="p-2">
				<div class="logoBox d-flex justify-content-center"><a href="/"><img src="images/logo.png" alt=""></a></div>
			</nav>
		</div>
	</header>



	<!--=====================================
	=        End of Header section          =
	======================================-->
	

	<div class="catBackgroundGradient">

		<form>

			<div class="cartWrapper checkOutWrapper">
				<div class="container">
					<div class="countDown"> Оставащо време: <span class="time">00:20:20</span></div>
				</div>
				<div class="container">
					<div class="leftColumn">
						<div class="addressForm">
							<h3 class="title">Адрес за доставка</h3>
							<div class="inputRow">
								<div class="inputCol6">
									<label for="firstName2">Име</label>
									<input id="firstName2" type="text" name="fname" placeholder="Въведи име*">
								</div>
								<div class="inputCol6">
									<label for="lastName2">Фамилия</label>
									<input id="lastName2" type="text" name="lname" placeholder="Въведи фамилия*">
								</div>
							</div>
							<div class="inputRow">
								<div class="inputCol12">
									<label for="phoneNumber">Телефонен номер</label>
									<input id="phoneNumber" type="text" name="phone_number" placeholder="Въведи номер*">
								</div>
							</div>
							<div class="inputRow">
								<div class="inputCol8">
									<label for="address">Адрес</label>
									<input id="address" type="text" name="address" placeholder="Въведи адрес*">
								</div>
								<div class="inputCol4">
									<label for="zipCode">Пощенски код</label>
									<input id="zipCode" type="text" name="zip_code" placeholder="Въведи пощенски код*">
								</div>
							</div>
						</div>
						<div class="paymentForm">
							<h3 class="title">Данни за плащане</h3>
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
						</div>
					</div>
					<div class="rightColumn">
						<div class="orderWrapper">
							<div class="order">
								<span class="title">Твоята поръчка</span>
								<div class="orderRow">
									<span class="left">Стойност</span>
									<span class="right">199.98 лв.</span>
								</div>
								<div class="orderRow">
									<span class="left">Доставка</span>
									<span class="right">0.00 лв.</span>
								</div>
								<div class="totalContainer">
									<div class="orderRow">
										<span class="left">ОБЩО</span>
										<span class="right">199.98 лв.</span>
									</div>
								</div>
								<div class="actionsContainer">
									<div class="submitBtnHolder">
										<button class="submitBtn">Плати сега</button>
									</div>
								</div>

								<!-- Ako e minala validaciqta i vsichko e ok, da se dobavi na button-a class="success" -->
								<!-- Ako e minala validaciqta da se dobavi na korektno popylnenite inputi class="successInput" -->
								<!-- Ako e minala validaciqta da se dobavi na nepopylnenite inputite class="errorInput" -->
								
							</div>
						</div>
					</div>
				</div>
			</div>

		</form>

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
						slidesToShow: 4,
						slidesToScroll: 4,
					}
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
				]
			});



			// var galleryTop = new Swiper('.gallery-top', {
			// 	spaceBetween: 10,
			// 	navigation: {
			// 		nextEl: '.swiper-button-next',
			// 		prevEl: '.swiper-button-prev',
			// 	},
			// 	loop: true,
			// 	loopedSlides: 3
			// });
			// var galleryThumbs = new Swiper('.gallery-thumbs', {
			// 	spaceBetween: 10,
			// 	centeredSlides: true,
			// 	slidesPerView: 'auto',
			// 	touchRatio: 0.2,
			// 	slideToClickedSlide: true,
			// 	loop: true,
			// 	loopedSlides: 3
			// });

			// galleryTop.controller.control = galleryThumbs;
			// galleryThumbs.controller.control = galleryTop;

			// $.fancybox.defaults.backFocus = false;

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

				$('.prodQuantityCart').select2({
					minimumResultsForSearch: -1,
					placeholder: "К-во",
				});

			});

		</script>


		<footer>
			<div class="bottomFooter">
				<div class="container">
					<div class="footerRow footerRowCheckout">
						<nav>
							<ul class="footerMenu2">
								<li><a href="">Условия за ползване</a></li>
								<li><a href="">Политика на конфиденциалност</a></li>
								<li><a href="">Бисквитки</a></li>
							</ul>
						</nav>
						<div class="logoFooter">
							<span class="text">© 2019 Arthabeauty</span>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</body>
	</html>