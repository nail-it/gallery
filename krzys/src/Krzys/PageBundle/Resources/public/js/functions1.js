
function checkedFilter(value) {
	if(value == 1) {
		$('.selected1').show();
		$('.selected').show(); // valid till 6.01.2015
		$('.selected').each(function() {
			$(this).find('a.regular-image').attr('data-fancybox-group', 'button');
		});
		$('.selected1').each(function() {
			$(this).find('a.regular-image').attr('data-fancybox-group', 'button');
		});		
		$('.selected2').hide();
		$('.selected2').each(function() {
			$(this).find('a.regular-image').attr('data-fancybox-group', '');
		});
		$('.son').show();
		$('.dau').hide();
	} else {
		if(value == 2) {
			$('.selected2').show();
			$('.selected2').each(function() {
				$(this).find('a.regular-image').attr('data-fancybox-group', 'button');
			});			
			$('.selected1').hide();
			$('.selected1').each(function() {
				$(this).find('a.regular-image').attr('data-fancybox-group', '');
			});
			$('.selected').hide(); // valid till 6.01.2015			
			$('.selected').each(function() {
				$(this).find('a.regular-image').attr('data-fancybox-group', '');
			});
			$('.son').hide();
			$('.dau').show();			
		} else {
			$('.item-box').show();
			$('.admin-item').show();
			$('.symfony-content').find('a.regular-image').attr('data-fancybox-group', 'button');
			$('.son').show();
			$('.dau').show();			

		}
	}	
}

function popupAlert(text, type) {
	$("#alert").attr('class', 'bg-'+type);
	$("#alert").html(text);
	$("#alert").show().delay(5000).fadeOut();	
}

function changeDimensionsForRegularImage() {
    $('.regular-image').each(function() {
    	var s = $(this).attr('href').replace(/\?.*$/, '');
		$(this).attr('href', s +'?width='+($(window).width())+'&height='+($(window).height()));
    });			
}

$(document).on('click', function() {
	changeDimensionsForRegularImage();
});

window.addEventListener("orientationchange", function() {
	changeDimensionsForRegularImage();
}, false);

$(document).ready(function() {
	$('.rotate-left').on('click', function() { 
	    //$(this).children('img').hide();
	    //$(this).append('<div class="ajax-loader"></div>');
	    
		var url = $(this).attr('id');
	    var me = $(this);
	    var rr = $.post(url, {  },
	      function(data) {
	    	me.parent().prev().children('img').fadeOut('500', function() { $(this).remove(); me.parent().prev().append(data.image).hide().fadeIn('2000'); });
	    });
	    return false; 
	});	
	
	$('.rotate-right').on('click', function() { 
	    var url = $(this).attr('id');
	    var me = $(this);
	    var rr = $.post(url, {  },
	      function(data) {
	    	me.parent().prev().children('img').fadeOut('500', function() { $(this).remove(); me.parent().prev().append(data.image).hide().fadeIn('2000'); });
	    });
	    return false; 
	});

	$('.hide-pic').on('click', function() { 
	    var url = $(this).attr('id');
	    var me = $(this);
	    var rr = $.post(url, {  },
	      function(data) {
	    	me.parent().parent().fadeOut('500');
	    });
	    return false; 
	});

	var dataAmount = 0;
	var incr = 0;
	var url = '';

	function doAjax() {
		if(incr < dataAmount) {
			var rr2 = $.post(url).done(function (data) {
				incr++;
				$('#amount-current').html(parseInt($('#amount-current').html())+1);
				doAjax();
			});
		}
	}

	$('.incoming-process').on('click', function() {
		$('#incoming-block').show();
		var url2 = $(this).attr('id');
		url = $(this).attr('title');

		var rr = $.post(url2).done(function(data) {
			dataAmount = data.amount;
			$('#amount-all').html(dataAmount);
			doAjax();
		});
		return false;
	});
/*
	$('.tag-all').on('click', function() {
		var url = $(this).attr('id');
		var rr = $.post(url, {  },
			function(data) {

			});
	});
*/
/*
	$('.date').on('click', function() {
		$(this).nextUntil("div.date").toggle();
	});

	$('.admin-header').on('click', function() {
		$(this).nextUntil(".admin-header").toggle();
	});
*/

	$(".admin-picture").on('mousedown', function(e) { 
	   var a = null;
	   var s = '';
	   if( (e.which == 1) ) {
	     if($(this).parent().hasClass('selected1')) {
	       $(this).parent().removeClass('selected1');
	       a = 'r';
	       s = 1;
	     } else {
	       $(this).parent().addClass('selected1');
	       a = 'a';
	       s = 1;
	     }
	     $(this).parent().removeClass('selected2');
	   }if( (e.which == 3) ) {
		 if($(this).parent().hasClass('selected2')) {
		   $(this).parent().removeClass('selected2');
	       a = 'r';
	       s = 2;	   
		 } else {
		   $(this).parent().addClass('selected2');
	       a = 'a';
	       s = 2;	   
		 }
		 $(this).parent().removeClass('selected1');
	   }else if( (e.which == 2) ) {
		   $(this).parent().removeClass('selected1');
		   $(this).parent().removeClass('selected2');
	       a = 'r';
	   }
	   var url = $(this).attr('id') + '?action='+a+'&select='+s;
	   var rr = $.post(url, {  },
	     function(data) {
	   		
	   });   
	   e.preventDefault();
	}).on('contextmenu', function(e){
	 e.preventDefault();
	});	
	
	$('div.item').mouseenter(function() {
      $(this).find('span').stop(true, true).slideUp();
	}).mouseleave(function(){
	  $(this).find('span').stop(true, true).slideDown();
	});
	
	$('.open-months').click(function() {
	  $('.open-months').removeClass('active');
	  $(this).addClass('active');
	  
	  $('.calendar-months').hide();
	  $('.calendar-months-'+$(this).attr('title')).show();
	});
	
	$('input.description').keyup(function(event) {
	  var url = $(this).attr('id');
      $.post(url, { description: $(this).val() },
  	    function(data) {
	    });
	});

	$('input.description-day').keyup(function(event) {
	  var url = $(this).attr('id');
      $.post(url, { description: $(this).val() },
  	    function(data) {
	    });
	});

	$('.evolution-add').click(function() {
	    var url = $(this).attr('id');
	    var rr = $.post(url, {  },
   	      function(data) {
	    	popupAlert('Added to evolution successfully!', 'success');
   	      });
   	    return false; 
	    		
	});	
	
	$('.actions span').hover(function() {
		$(this).addClass('hover');
	}, function() {
		$(this).removeClass('hover');
	})
	
	$('.best-add').click(function() {
	    var url = $(this).attr('id');
	    var rr = $.post(url, {  },
   	      function(data) {
	    	popupAlert('Added to best successfully!', 'success');
   	      });
   	    return false;	
	});	
	
	$('#filter > div').click(function() {
		$(this).children('input').attr('checked', true);
	    var url = $(this).parent().attr('title') + '?tag=' + $(this).children('input').attr('value');
	    var rr = $.post(url, {  },
	      function(data) {
	    	
	    });
	    checkedFilter($(this).children('input').attr('value'));
	});
	
	
	$('#filter > div > input').each(function() {
		if($(this).attr('checked') == 'checked') {
			checkedFilter($(this).attr('value'));			
		}
	});
	
	/* fancybox */
	$('.regular-image').fancybox({
		padding         : 0,
		margin          : 0,
		type			: 'ajax',
		prevEffect		: 'fade',
		nextEffect		: 'fade',
		openOpacity		: 0,
		closeOpacity	: 0,
		scrolling		: 'no',
		autoSize		: true,
		closeBtn		: false,
		playSpeed		: 5000,
		afterShow : function() {
	    	var src = this.element.find('img').parent().attr('href');
	    	var fancyImage = this;
			setTimeout(function(){  
		        fancyImage.parent.find('#fancybox-buttons').find('.btnToggle').attr('href', src);
		        //if(fancyImage.parent.find('.fancybox-wrap').height() + 30 > $( window ).height()) {
		        //	fancyImage.parent.find('.fancybox-wrap').css('top', '0');
		        //}
			}, 1000);
	    },
		onUpdate 		: function(opts, obj) { 
			$.fancybox.reposition();			
		},
		helpers		: { 
			title	: { type : 'inside' },
			buttons	: { }
		},
	});
	
	changeDimensionsForRegularImage();
	
});