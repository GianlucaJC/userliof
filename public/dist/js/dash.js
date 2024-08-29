$(document).ready( function () {
	//$('body').addClass("sidebar-collapse");
    var table=$('#tb_utenti').DataTable({
		pagingType: 'full_numbers',
		dom: 'lBfrtip',
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
            lengthMenu: 'Visualizza _MENU_ nominativi per pagina',
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
	if (!confirm("Sicuri di abilitare l'utente?")) return false;
	base_path = $("#url").val();

	const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
	const csrf = metaElements.length > 0 ? metaElements[0].content : "";

	$.ajax({
		type: 'POST',
		url: base_path+"/enable_user",
		data: {_token: csrf,id_user:id_user},
		success: function (data) {
			resp=JSON.parse(data)
			// if (resp.response=="OK")
			$("#tr"+id_user).addClass("canc")
			html=`
				<center>
					<a href='dashboard'>
					<button type='button' class="btn btn-primary btn-sm" onclick="">
					<i class="fas fa-sync-alt"></i> Refresh
					</button>
					</a>
				</center>
			`
			$("#td"+id_user).html(html)	
			
		}
	});		


}

function disable_user(id_user) {
	if (!confirm("Sicuri di disabilitare l'utente?")) return false;
	base_path = $("#url").val();

	
	const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
	const csrf = metaElements.length > 0 ? metaElements[0].content : "";

	$.ajax({
		type: 'POST',
		url: base_path+"/disable_user",
		data: {_token: csrf,id_user:id_user},
		success: function (data) {
			resp=JSON.parse(data)
			// if (resp.response=="OK")
			$("#tr"+id_user).addClass("canc")
			html=`
				<center>
					<a href='dashboard'>
					<button type='button' class="btn btn-primary btn-sm" onclick="">
					<i class="fas fa-sync-alt"></i> Refresh
					</button>
					</a>
				</center>
			`
			$("#td"+id_user).html(html)	
			
		}
	});		


}

function edit_user(id_ref) {
	$("#div_elenco").hide()
	window.moduloEdit.id_user=id_ref
}

