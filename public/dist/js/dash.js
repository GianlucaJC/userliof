$(document).ready( function () {
	//$('body').addClass("sidebar-collapse");
    var table=$('#tb_utenti').DataTable({
		pagingType: 'full_numbers',
		dom: 'lfrtBip',
		buttons: [
			'excel','pdf'
		],		
		searching: true,
		paging:true,
		filter: true,
		info: true,
		pageLength: 10,
		lengthMenu: [10, 15, 20, 50, 100, 200, 500],

		
		
        language: {
            lengthMenu: 'Visualizza _MENU_ utenti per pagina',
            zeroRecords: 'Nessun utente trovato',
            info: 'Pagina _PAGE_ di _PAGES_',
            infoEmpty: 'Non sono disponibili utenti',
            infoFiltered: '(Filtrati da _MAX_ utenti totali)',
        },

		
    });	
	
	/*
    var table = $('#tb_utenti').DataTable();
	$('#tbl_pns').on( 'page.dt', function () {
		var info = table.page.info();
		page=parseInt(info.page)
		$("#cur_page").val(page)
	})
	
	cur_page=$("#cur_page").val()
	page=parseInt(cur_page)
	//$("#tbl_pns").dataTable().fnPageChange(cur_page,true);
	table.page(page).draw(false);
	*/
	
} );


function enable_user(id_user) {
	Swal.fire({
		title: "Sicuri di abilitare l'utente?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Sì, abilita!',
		cancelButtonText: 'Annulla'
	}).then((result) => {
		if (result.isConfirmed) {
			base_path = $("#url").val();
			const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
			const csrf = metaElements.length > 0 ? metaElements[0].content : "";
			$.ajax({
				type: 'POST',
				url: base_path+"/enable_user",
				data: {_token: csrf,id_user:id_user},
				success: function (data) {
					// NOTA: Si presume che il server restituisca l'oggetto utente completo in formato JSON.
					Swal.fire({
						title: 'Abilitato!',
						text: "L'utente è stato abilitato con successo.",
						icon: 'success',
						timer: 1500,
						showConfirmButton: false
					});

					var table = $('#tb_utenti').DataTable();
					var row = table.row('#tr' + id_user);

					if ($('#view_dele').is(':checked')) {
						// Se stiamo visualizzando solo i disabilitati,
						// l'utente appena abilitato non deve più essere in questa lista.
						row.remove().draw(false);
					} else {
						// Altrimenti, siamo nella vista standard. Aggiorniamo o aggiungiamo la riga.

						// Ricrea i bottoni, rispettando la logica per l'utente con ID 1
						var buttonsHtml;
						if (id_user != 1) {
							buttonsHtml = `
								<div class="d-flex flex-nowrap">
									<button type='button' class="btn btn-warning btn-sm me-1" onclick="disable_user(${id_user})">
										<i class="fas fa-user-slash"></i> Disabilita
									</button>
									<button type='button' class="btn btn-success btn-sm" onclick="edit_user(${id_user})">
										<i class="fas fa-user-cog"></i> Modifica
									</button>
								</div>
							`;
						} else {
							buttonsHtml = `
								<div class="d-flex flex-nowrap">
									<button type='button' class="btn btn-success btn-sm" onclick="edit_user(${id_user})">
										<i class="fas fa-user-cog"></i> Modifica
									</button>
								</div>
							`;
						}

						if (row.any()) {
							// La riga esiste (era visibile ma disabilitata), quindi la aggiorniamo
							var rowNode = row.node();
							$(rowNode).find('td').removeClass('canc');
							$('#td' + id_user).html(buttonsHtml);
						} else {
							// La riga non esiste (era nascosta), quindi la aggiungiamo di nuovo.
							// Questo richiede che il server restituisca i dati dell'utente (nella variabile 'data').
							var newRowNode = table.row.add([
								buttonsHtml,
								data.userid,
								`<i>${data.operatore}</i>`,
								data.email
							]).draw(false).node();
							
							// Aggiunge gli ID alla nuova riga e alla sua prima cella per operazioni future
							$(newRowNode).attr('id', 'tr' + id_user);
							$(newRowNode).find('td:first').attr('id', 'td' + id_user);
						}
					}
				},
				error: function () {
					Swal.fire('Errore!', "Si è verificato un problema durante l'abilitazione.", 'error');
				}
			});		
		}
	})
}

function disable_user(id_user) {
	Swal.fire({
		title: "Sicuri di disabilitare l'utente?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Sì, disabilita!',
		cancelButtonText: 'Annulla'
	}).then((result) => {
		if (result.isConfirmed) {
			base_path = $("#url").val();
			const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
			const csrf = metaElements.length > 0 ? metaElements[0].content : "";
			$.ajax({
				type: 'POST',
				url: base_path+"/disable_user",
				data: {_token: csrf,id_user:id_user},
				success: function (data) {
					Swal.fire({
						title: 'Disabilitato!',
						text: "L'utente è stato disabilitato con successo.",
						icon: 'success',
						timer: 1500,
						showConfirmButton: false
					});

					var table = $('#tb_utenti').DataTable();
					var row = table.row('#tr' + id_user);

					if ($('#view_dele').is(':checked')) {
						// Se "mostra disabilitati" è attivo, aggiorna la riga per mostrarla come disabilitata
						var rowNode = row.node();
						$(rowNode).find('td:eq(1), td:eq(2)').addClass('canc');

						var newButtonHtml = `
							<div class="text-center">
								<button type='button' onclick="enable_user(${id_user})" class="btn btn-primary btn-sm">
									<i class="fas fa-user-plus"></i> Abilita
								</button>
							</div>
						`;
						$('#td' + id_user).html(newButtonHtml);
					} else {
						// Altrimenti, rimuovi la riga dalla tabella
						row.remove().draw(false);
					}
				},
				error: function () {
					Swal.fire('Errore!', 'Si è verificato un problema durante la disabilitazione.', 'error');
				}
			});		
		}
	})
}

function edit_user(id_ref) {
	window.moduloEdit.id_user = id_ref;
	$('#modalvalue').modal('show');
}
