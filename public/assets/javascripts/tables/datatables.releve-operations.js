(function( $ ) {

	'use strict';

	var datatableOperationsNRInit = function() {

		$('#datatable-operationsNR').dataTable();

	};

    var datatablOperationInit = function() {

		$('#datatable-operations').dataTable();

	};

    var datatablEcheancesInit = function() {

		$('#datatable-echeances').dataTable();

	};

	$(function() {
		datatableOperationsNRInit();
        datatablEcheancesInit();
        datatablOperationInit();
	});

}).apply( this, [ jQuery ]);