require.config({
	paths: {
		jquery:              '../bower_components/jquery/dist/jquery',
		underscore:          '../bower_components/underscore/underscore',
		bootstrapAffix:      '../bower_components/sass-bootstrap/js/affix',
		bootstrapAlert:      '../bower_components/sass-bootstrap/js/alert',
		bootstrapButton:     '../bower_components/sass-bootstrap/js/button',
		bootstrapCarousel:   '../bower_components/sass-bootstrap/js/carousel',
		bootstrapCollapse:   '../bower_components/sass-bootstrap/js/collapse',
		bootstrapDropdown:   '../bower_components/sass-bootstrap/js/dropdown',
		bootstrapModal:      '../bower_components/sass-bootstrap/js/modal',
		bootstrapPopover:    '../bower_components/sass-bootstrap/js/popover',
		bootstrapScrollspy:  '../bower_components/sass-bootstrap/js/scrollspy',
		bootstrapTab:        '../bower_components/sass-bootstrap/js/tab',
		bootstrapTooltip:    '../bower_components/sass-bootstrap/js/tooltip',
		bootstrapTransition: '../bower_components/sass-bootstrap/js/transition',
		jqueryVimeoEmbed:    '../bower_components/jquery-smart-vimeo-embed/jquery-smartvimeoembed'
	},
	shim: {
		bootstrapAffix: {
			deps: [
				'jquery'
			]
		},
		bootstrapAlert: {
			deps: [
				'jquery'
			]
		},
		bootstrapButton: {
			deps: [
				'jquery'
			]
		},
		bootstrapCarousel: {
			deps: [
				'jquery'
			]
		},
		bootstrapCollapse: {
			deps: [
				'jquery',
				'bootstrapTransition'
			]
		},
		bootstrapDropdown: {
			deps: [
				'jquery'
			]
		},
		bootstrapPopover: {
			deps: [
				'jquery'
			]
		},
		bootstrapScrollspy: {
			deps: [
				'jquery'
			]
		},
		bootstrapTab: {
			deps: [
				'jquery'
			]
		},
		bootstrapTooltip: {
			deps: [
				'jquery'
			]
		},
		bootstrapTransition: {
			deps: [
				'jquery'
			]
		},
		jqueryVimeoEmbed: {
			deps: [
				'jquery'
			]
		}
	}
});

require( [ 'jquery', 'underscore', 'bootstrapTransition', 'bootstrapCollapse', 'searchMode', 'bootstrapTab', 'bootstrapCarousel', 'jqueryVimeoEmbed' ], function ( $, _ ) {
	'use strict';

	$( '.vimeo-thumb' ).each( function () {
		$( this ).smartVimeoEmbed( _( {
			width: $( this ).data( 'width' )
		} ).defaults( { width: 640 } ) );
	} );

	var screenWidth = function () {
		return ( window.innerWidth > 0 ) ? window.innerWidth : screen.width;
	};

	$( window ).on( 'resize', _.debounce( function () {
		if ( screenWidth() > 991 ) {
			$( '#readable-navbar-collapse' )
				.removeAttr( 'style' )
				.removeClass( 'in' );
		}
	}, 500 ) );
} );