$(document).ready(function(){
	$("tbody").sortable({
		helper: fixWidthHelper
	}).disableSelection();
})

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function onStop() {
	var params = "";
	var imagesLI = $(".imagesListLI");
	$.each(imagesLI, function(i){
		params += "imgID[]=" + i + "_" + this.id;
		if(imagesLI[i+1]) params += "&";
	});
	
	$.ajax({
		type: "GET",
		url: "imagesList.php",
		data: "action=move&" + params
	});
}
