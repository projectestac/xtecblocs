jQuery( document ).ready( function( $ ) {
	
	// Masonry
	$wrapper = $( '.posts' );
	if ( $wrapper.length ) {
		$grid = $wrapper.imagesLoaded( function() {
			$grid = $wrapper.masonry( {
				itemSelector:		'.post-container',
				stagger:			0,
				transitionDuration: 0,
			} );
			$( '.post-container' ).animate( { 'opacity': 1 }, 300 );
		} );
	}

	// Toggle navigation
	$( ".nav-toggle" ).on( "click", function() {
		$( this ).toggleClass( "active" );
		$( ".mobile-navigation" ).slideToggle();
	} );
	
	
	// Hide mobile-menu > 1000
	$( window ).resize( function() {
		if ( $( window ).width() > 1000 ) {
			$( ".nav-toggle" ).removeClass( "active" );
			$( ".mobile-navigation" ).hide();
		}
	} );

    
	// Load Flexslider
	function runFlexslider() {
		$( ".flexslider" ).flexslider( {
			animation: "slide",
			controlNav: false,
			smoothHeight: false,
			start: function() {
				$grid.masonry();
			},
		} );
	}

	runFlexslider();

        			
	// Resize videos to fit the container, while maintaining the aspect ratio
	var vidSelector = ".post iframe, .post object, .post video, .widget-content iframe, .widget-content object, .widget-content iframe";
	var resizeVideo = function(sSel) {
		$( sSel ).each( function() {
			var $video = $( this ),
				$container = $video.parent(),
				iTargetWidth = $container.width();

			if ( ! $video.attr( "data-origwidth" ) ) {
				$video.attr( "data-origwidth", $video.attr( "width" ) );
				$video.attr( "data-origheight", $video.attr( "height" ) );
			}

			var ratio = iTargetWidth / $video.attr( "data-origwidth" );

			$video.css( "width", iTargetWidth + "px" );
			$video.css( "height", ( $video.attr( "data-origheight" ) * ratio ) + "px" );
		} );
	}

	resizeVideo( vidSelector );

	$( window ).resize( function() {
		resizeVideo(vidSelector);
	} );
    
	
	// When Jetpack Infinite scroll posts have loaded
	$( document.body ).on( 'post-load', function() {
		
		$wrapper.imagesLoaded( function() {
			$wrapper.masonry( {
				itemSelector: '.post-container'
			} );
		} );

		resizeVideo( vidSelector );
		
		runFlexslider();

		$wrapper.masonry( 'reloadItems' ).on( 'layoutComplete', function() {
			$( '.post-container' ).animate( { 'opacity': 1 }, 300 );
		} );

	} );
	
} );