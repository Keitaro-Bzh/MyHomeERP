(function( $ ) {

	'use strict';

	var datatableContactInit = function() {

		$('#datatable-contacts').dataTable();

	};

    var datatablSocieteInit = function() {

		$('#datatable-societes').dataTable();

	};

	$(function() {
		datatableContactInit();
        datatablSocieteInit();
	});

}).apply( this, [ jQuery ]);