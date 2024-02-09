	$(function() {
		// there's the gallery and the best-add
		var $gallery = $("#gallery");
		var	$bestadd = $("#best-add");

		// let the gallery items be draggable
		$( ".admin-picture", $gallery ).draggable({
			cancel: "a.ui-icon", // clicking an icon won't initiate dragging
			revert: "invalid", // when not dropped, the item will revert back to its initial position
			containment: $( "#demo-frame" ).length ? "#demo-frame" : "document", // stick to demo-frame if present
			helper: "clone",
			cursor: "move"
		});

		// let the bestadd be droppable, accepting the gallery items
		$bestadd.droppable({
			accept: "#gallery .admin-picture",
			activeClass: "ui-state-highlight",
			drop: function( event, ui ) {
				bestImage( ui.draggable );
				// send ajax
				var url = "/bestAdd"+ui.draggable.context.attributes.title.nodeValue;
	  	        $.post(url, { },
			  	  function(data) {
				});
				
			}
		});

		// let the gallery be droppable as well, accepting items from the best-add
		$gallery.droppable({
			accept: "#best-add li",
			activeClass: "custom-state-active",
			drop: function( event, ui ) {
				recycleImage( ui.draggable );
			}
		});

		// image deletion function
		var recycle_icon = "<a href='link/to/recycle/script/when/we/have/js/off' title='Recycle this image' class='ui-icon ui-icon-refresh'>Recycle image</a>";
		
		function bestImage( $item ) {
			$item.fadeOut(function() {
				var $list = $( "ul", $bestadd ).length ?
					$( "ul", $bestadd ) :
					$( "<ul class='gallery ui-helper-reset'/>" ).appendTo( $bestadd );

				$item.find( "a.ui-icon-best-add" ).remove();
				$item.append( recycle_icon ).appendTo( $list ).fadeIn(function() {
					$item
						.animate({ width: "38px" })
						.find( "img" )
							.animate({ height: "26px" });
				});
			});
		}

		// image recycle function
		var bestadd_icon = "<a href='link/to/best-add/script/when/we/have/js/off' title='Delete this image' class='ui-icon ui-icon-best-add'>Delete image</a>";
		function recycleImage( $item ) {
			$item.fadeOut(function() {
				$item
					.find( "a.ui-icon-refresh" )
						.remove()
					.end()
					.css( "width", "96px")
					.append( bestadd_icon )
					.find( "img" )
						.css( "height", "72px" )
					.end()
					.appendTo( $gallery )
					.fadeIn();
			});
		}

		// image preview function, demonstrating the ui.dialog used as a modal window
		function viewLargerImage( $link ) {
			var src = $link.attr( "href" ),
				title = $link.siblings( "img" ).attr( "alt" ),
				$modal = $( "img[src$='" + src + "']" );

			if ( $modal.length ) {
				$modal.dialog( "open" );
			} else {
				var img = $( "<img alt='" + title + "' width='384' height='288' style='display: none; padding: 8px;' />" )
					.attr( "src", src ).appendTo( "body" );
				setTimeout(function() {
					img.dialog({
						title: title,
						width: 400,
						modal: true
					});
				}, 1 );
			}
		}

		// resolve the icons behavior with event delegation
		$( "ul.gallery > li" ).click(function( event ) {
			var $item = $( this ),
				$target = $( event.target );

			if ( $target.is( "a.ui-icon-best-add" ) ) {
				deleteImage( $item );
			} else if ( $target.is( "a.ui-icon-zoomin" ) ) {
				viewLargerImage( $target );
			} else if ( $target.is( "a.ui-icon-refresh" ) ) {
				recycleImage( $item );
			}

			return false;
		});
	});