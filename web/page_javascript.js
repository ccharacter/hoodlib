
$(document).ready(function() { 

	$(".sws-cell").hover(function() { 
		var id = '#sws-info-' + $(this).attr('id').replace('sws-cell-', '');
		var newHTML = $(id).html();
		$("#sws-details").html(newHTML);
	}, function() { 
		//$("#sws-details").empty(); 
	}); 
	

	
	/*$(".abstract").click(function(){              
		alert("abstract");
		$(".full-abstract").show(); 
		$(".abstract").hide(); 
	 }); 
	 $(".full-abstract").click(function(){              
		$(".full-abstract").hide(); 
		$(".abstract").show(); 
	 });  */	
	
	
}); 

function swsShow(show,hide) {
	// alert(show+"|"+hide);
	$("#"+show).removeClass('sws-hide');
	$("#"+show).addClass('sws-show');

	$("#"+hide).removeClass('sws-show');
	$("#"+hide).addClass('sws-hide');
}

function bindEvent(element, eventName, eventHandler) {
	if (element.addEventListener){
		element.addEventListener(eventName, eventHandler, false);
	} else if (element.attachEvent) {
		element.attachEvent('on' + eventName, eventHandler);
	}
}

// Listen to message from child window
bindEvent(window, 'message', function (e) {
	var incoming = e.data;
	var incArr = incoming.split('|');
	var myID = '#sws-placeholder-' + incArr[0];
	var myURL = incArr[1];
	//console.log(myID + ' - ' + myURL);
	
	var newHTML = '<div class="r-icon"><a href="' + myURL + '" target="_blank"><span class="fas fa-book-reader"></span></a></div>';

	
	$(myID).html(newHTML);
	
});
