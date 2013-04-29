	jQuery(function($) {	
		// Init Nivo Slider		
		if ($("#slider").length > 0){
			$('#slider').nivoSlider({
				effect:'random', // Specify sets like: 'fold,fade,sliceDown'
				pauseTime:4000, // How long each slide will show
				captionOpacity:false, // Universal caption opacity
				directionNav:false, // Next & Prev navigation
				directionNavHide:false, // Only show on hover
				controlNavThumbs:false, // Use thumbnails for Control Nav
				controlNavThumbsFromRel:true // Use image rel for thumbs
			});
		}
		// Init Superfish menu
		$('.sf-menu').superfish();

		if($('#headline-slider a').length > 0){
			var calcDelay = function(str){
				return str.length * 150;
			}

			var headlineSliderNext = function(){
				var $prev = $('#headline-slider a:visible');
				if($prev.length > 0){
					var $next = $prev.next();
					if($next.length < 1)
					    $next = $('#headline-slider a').first();
					    
					$prev.fadeOut('slow',function(){
						$next.fadeIn('slow', function(){
							setTimeout(
								headlineSliderNext,
								calcDelay($next.html())
							);
						});
					});
				}else{
					var $next = $('#headline-slider a').first();
					$next.fadeIn('slow', function(){
						setTimeout(
							headlineSliderNext,
							calcDelay($next.html())
						);
					});
				}
			}

			headlineSliderNext();
		}
	});
