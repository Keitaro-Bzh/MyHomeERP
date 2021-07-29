(function( $ ) {

	'use strict';

	var datatableOperationsNRInit = function() {
		$('#datatable-operationsNR').dataTable({
			"order": [[ 0, "desc" ]],
			"lengthMenu": [[25, 50, -1], [25, 50, "All"]],
			language: {
				processing:     "Traitement en cours...",
				info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
				infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				infoPostFix:    "",
				loadingRecords: "Chargement en cours...",
				zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
				emptyTable:     "Aucune donnée disponible dans le tableau",
				paginate: {
					first:      "Premier",
					previous:   "Pr&eacute;c&eacute;dent",
					next:       "Suivant",
					last:       "Dernier"
				},
				aria: {
					sortAscending:  ": activer pour trier la colonne par ordre croissant",
					sortDescending: ": activer pour trier la colonne par ordre décroissant"
				}
			}
		});
	};

    var datatablOperationInit = function() {
		$('#datatable-operations').dataTable({
			"order": [[ 0, "desc" ]],
			"lengthMenu": [[25, 50, -1], [25, 50, "All"]],
			language: {
				processing:     "Traitement en cours...",
				info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
				infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				infoPostFix:    "",
				loadingRecords: "Chargement en cours...",
				zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
				emptyTable:     "Aucune donnée disponible dans le tableau",
				paginate: {
					first:      "Premier",
					previous:   "Pr&eacute;c&eacute;dent",
					next:       "Suivant",
					last:       "Dernier"
				},
				aria: {
					sortAscending:  ": activer pour trier la colonne par ordre croissant",
					sortDescending: ": activer pour trier la colonne par ordre décroissant"
				}
			}
		});
	};

    var datatablEcheancesInit = function() {
		$('#datatable-echeances').dataTable({
			"order": [[ 0, "desc" ]],
			"lengthMenu": [[25, 50, -1], [25, 50, "All"]],
			language: {
				processing:     "Traitement en cours...",
				info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
				infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				infoPostFix:    "",
				loadingRecords: "Chargement en cours...",
				zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
				emptyTable:     "Aucune donnée disponible dans le tableau",
				paginate: {
					first:      "Premier",
					previous:   "Pr&eacute;c&eacute;dent",
					next:       "Suivant",
					last:       "Dernier"
				},
				aria: {
					sortAscending:  ": activer pour trier la colonne par ordre croissant",
					sortDescending: ": activer pour trier la colonne par ordre décroissant"
				}
			}
		});
	};

	$(function() {
		datatableOperationsNRInit();
        datatablEcheancesInit();
        datatablOperationInit();
	});

}).apply( this, [ jQuery ]);