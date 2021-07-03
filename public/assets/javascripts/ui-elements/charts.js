
(function( $ ) {

	'use strict';

	/*
	Morris: Bar
	*/
	Morris.Bar({
		resize: true,
		element: 'morrisBar',
		data: morrisBarData,
		xkey: 'y',
		ykeys: ['a', 'b'],
		labels: ['Crédit', 'Débit'],
		hideHover: true,
		barColors: ['#0088cc', '#2baab1']
	});

}).apply( this, [ jQuery ]);