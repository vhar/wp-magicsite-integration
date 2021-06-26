var $ = jQuery.noConflict();
$( document ).on( 'click', '.magicsite-navigation-menu li.menu-item-has-children > a', function () {
	event.preventDefault();
	$(this).parent().toggleClass( 'magicsite-navigation-menu-opened' );
});

$( document ).ready( function () {
	$( '.magicsite-navigation-menu li.menu-item-has-children ul li' ).each( function () {
		if ( $( this ).hasClass( 'current-menu-item' ) ) {
			$( this ).parents( '.menu-item-has-children' ).addClass( 'magicsite-navigation-menu-opened' );
		}
	});
});
