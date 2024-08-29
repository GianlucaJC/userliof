$(document).ready( function () {
	//$('body').addClass("sidebar-collapse");
    var table=$('#tb_regole').DataTable({
		pagingType: 'full_numbers',
		dom: 'lBfrtip',
		buttons: [
			'excel','pdf'
		],		
		searching: true,
		paging:true,
		filter: true,
		info: true,
		pageLength: 8,
		lengthMenu: [8, 10, 15, 20, 50, 100, 200, 500],

		
		
        language: {
            lengthMenu: 'Visualizza _MENU_ regole per pagina',
            zeroRecords: 'Nessuna regola trovata',
            info: 'Pagina _PAGE_ di _PAGES_',
            infoEmpty: 'Non sono disponibili regole',
            infoFiltered: '(Filtrati da _MAX_ regole totali)',
        },

		
    });	
	/*
	table.on('draw', function () {
		var tr = table.row(':eq(2)').node();
		tr.classList.add('selected');
	});	
	*/
	
} );

function new_rule() {
	$("#div_elenco").hide()
	window.Regole.reset_all()
	window.Regole.id_edit="new"
	window.Regole.edit_pattern=true
	window.Regole.edit_inizia=true

}

function dele_rule(id_ref) {
	if (!confirm("Sicuri di cancellare la regola?")) return false;
	//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
	const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
	const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
	fetch("dele_rule", {
		method: 'post',
		headers: {
			"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
			"X-CSRF-Token": csrf
		},
		body: "id_dele="+id_ref
	})
	.then(response => {
		if (response.ok) {
			return response.json();
		}
	})
	.then(resp=>{

		if (resp.header=='OK') {
			$(".allrow").removeClass("selected")
			$("#tr"+id_ref).addClass("selected")
		
			var table = new DataTable('#tb_regole');
			var rows = table
				.rows('.selected')
				.remove()
				.draw();	
		}		


	})
	.catch(status, err => {
		return console.log(status, err);
	})	

}

function edit_rule(id_ref) {
	$("#div_elenco").hide()
	window.Regole.reset_all()
	window.Regole.id_edit=id_ref
	pattern=$("#id_regola"+id_ref).data("pattern")
	
	inizia_con=$("#id_regola"+id_ref).data("inizia_con")
	min_len=$("#id_regola"+id_ref).data("min_len")
	max_len=$("#id_regola"+id_ref).data("max_len")
	len=$("#id_regola"+id_ref).data("len")
	
	inidbmod=$("#id_regola"+id_ref).data("inidbmod")
	if (inidbmod=="") inidbmod="-"
	inidbmod1=$("#id_regola"+id_ref).data("inidbmod1")
	if (inidbmod1=="") inidbmod1="-"

	edit_pattern=false;edit_inizia=false
	btn_pattern=false;btn_inizia_con=false
	if (pattern && pattern.length>0) {
		btn_pattern=true;btn_inizia_con=false
		edit_pattern=true;edit_inizia=false
	} 
	else {
		if (inizia_con && inizia_con.length>0) {
			btn_pattern=false;btn_inizia_con=true
			edit_pattern=false;edit_inizia=true
		}
	}	

	window.Regole.edit_pattern=edit_pattern
	window.Regole.edit_inizia=edit_inizia
	window.Regole.btn_pattern=btn_pattern
	window.Regole.btn_inizia_con=btn_inizia_con

	window.Regole.pattern=pattern
	window.Regole.inizia_con=inizia_con
	window.Regole.min_len=min_len
	window.Regole.max_len=max_len
	window.Regole.len=len

	window.Regole.DBmodelli=inidbmod
	window.Regole.DBmodelli1=inidbmod1
	window.Regole.DBmod=inidbmod
	window.Regole.DBmod1=inidbmod1	


	window.Regole.iniDBmodelli=inidbmod
	window.Regole.iniDBmodelli1=inidbmod1
	window.Regole.iniDBmod=inidbmod
	window.Regole.iniDBmod1=inidbmod1	
	/*
	$(".refr").hide()
	$("#div_refr"+id_ref).show()
	*/
	
}

function testcodice() {
	html=`
		<div style='width:20%' class="mt-3 mb-3 d-flex align-items-center">
		<strong role="status">Attendere. Verifica in corso</strong>
		<div class="spinner-border ms-auto" aria-hidden="true"></div>
		</div>	
	`	
	$("#div_resp_test").html(html)
	$("#div_resp_test").show(120)
	setTimeout(() => {
		//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
		const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
		const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
		test_codice=$("#test_codice").val();
		fetch("testcodice", {
			method: 'post',
			headers: {
				"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
				"X-CSRF-Token": csrf
			},
			body: "test_codice="+test_codice
		})
		.then(response => {
			if (response.ok) {
				return response.json();
			}
		})
		.then(resp=>{
			console.log(resp)
			pattern=resp.test_pattern
			inizia=resp.test_inizia
			num_regole=0
			reg_ptn=0
			reg_ini=0
			$.each( pattern, function( key, value ) {
				reg_ptn++
				num_regole++
			});			
			$.each( inizia, function( key, value ) {
				reg_ini++
				num_regole++
			});	
			html=""
			html+="<div class='mt-2'>"
				if (num_regole==0) {
					html+=`<div class="alert alert-warning" role="alert">
							<b>Attenzione!</b> Nessuna regola trovata per il codice
						</div>`
				}	
				if (num_regole==1) {
					html+=`<div class="alert alert-success" role="alert">
							<b>Ottimo!</b> Regola trovata per il codice
						</div>`
				}	

				if (num_regole>1) {
					html+=`<div class="alert alert-warning" role="alert">
							<b>Attenzione!</b> Conflitto di regole riscontrate.<hr>
							<b>N.B.</b><i> Attualmente viene applicata la regola con IDregola più alta</i>
						</div>`
				}

				if (reg_ptn>0) {
					//tabella regole trovate (Pattern)
					html+=`<table class="table table-striped">
							<tr>
								<th>IDregola</th>
								<th>Pattern</th>
								<th>DBmodelli</th>							
								<th>DBmodelli1</th>
								<th>Data ultima modifica</th>
							</tr>	
						`
						$.each( pattern, function( ptn, value ) {
							html+=`<tr>
									<td>`+value.id+`</td>
									<td>`+value.pattern+`</td>
									<td>`+value.DBmodelli+`</td>
									<td>`+value.DBmodelli1+`</td>
									<td>`+value.updated_at+`</td>
								</tr>
							`
						});	
					html+="</table>"
				}

				if (reg_ini>0) {
					//tabella regole trovate (Inizia con)
					html+=`<table class="mt-2 table table-striped">
							<tr>
								<th>IDregola</th>
								<th>Inizia con</th>
								<th>MinLen</th>
								<th>MaxLen</th>
								<th>Len</th>
								<th>DBmodelli</th>							
								<th>DBmodelli1</th>
								<th>Data ultima modifica</th>
							</tr>	
						`
						$.each( inizia, function( inizia_con, value ) {
							console.log("inizia_con",inizia_con)
							console.log("value",value)
							html+=`<tr>
									<td>`+value.id+`</td>
									<td>`+value.inizia_con+`</td>
									<td>`+value.min_len+`</td>
									<td>`+value.max_len+`</td>
									<td>`+value.len+`</td>
									<td>`+value.DBmodelli+`</td>
									<td>`+value.DBmodelli1+`</td>
									<td>`+value.updated_at+`</td>
								</tr>		
							`
						});	
					html+="</table>"
				}
				
			html+=`<button type="button" class="btn btn-secondary" onclick="$('#div_resp_test').empty;$('#div_resp_test').hide(120)">Chiudi Test</button>`
		

			html+="</div><hr>"

			$("#div_resp_test").html(html)		

		})
		.catch(status, err => {
			return console.log(status, err);
		})	
	},300);
	
	
}