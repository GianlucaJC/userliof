<?php
use App\Models\User;
	$id = Auth::user()->id;
	$user = User::find($id);
	

?>
@extends('all_views.viewmaster.index')

@section('title', 'UserLiof')

@section('extra_style') 
	<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>	
@endsection

<style>
#tb_utenti {
    font-size: 12px;
    table-layout: fixed;

	.canc{
 	 color:red;
	 text-decoration: line-through;

}
}
 
td {
    word-wrap: break-word;
}
</style>

@section('operazioni')

<!-- Pannello gestione utenza !-->
	<div class="p-3">
		<h5>Impostazioni globali</h5>
		<p>
			<form method='post' action="{{ route('dashboard') }}" id='frm_global' name='frm_global' autocomplete="off" class="needs-validation" autocomplete="off">
				<div class="form-group">
					<label for="email_notif">Email notifiche edit/view/sign</label>
					<textarea class="form-control" id="email_notif" name='email_notif' rows="3" placeholder='Usare punto e virgola per separare le email'>{{$email_notif ?? ''}}</textarea>
				</div>

				<div class="form-group">
					<label for="email_notif_green">Email notifiche 'green'</label>
					<textarea class="form-control" id="email_notif_green" name='email_notif_green' rows="3" placeholder='Usare punto e virgola per separare le email'>{{$email_notif_green ?? ''}}</textarea>
				</div>
				
				
				<div class="form-group">
					<label for="codici_esclusi">Codici esclusi</label>
					<textarea class="form-control" id="codici_esclusi" name='codici_esclusi' rows="3" disabled placeholder='Usare punto e virgola per separare i codici'>{{$codici_esclusi ?? ''}}</textarea>					
				</div>

			<button type="submit" id="btn_save" name="btn_save" value="1" class="btn btn-primary">Salva impostazioni</button>
				
			</form>	
		</p>
    </div>
	
	
@endsection


@section('notifiche') 

	@if (1==2)
      <li class="nav-item dropdown notif" onclick="azzera_notif()">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">{{count($scadenze)}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">Avvisi di scadenza</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file-signature"></i> {{count($scadenze)}} {{$descr_num}} in scadenza
            <span class="float-right text-muted text-sm"></span>
          </a>
          <div class="dropdown-divider"></div>

          <div class="dropdown-divider"></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Vai al dettaglio</a>
        </div>
      </li>
	@endif  
@endsection

@section('content_main')

  <!-- Content Wrapper. Contains page content -->


  
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">UserLiof - Definizione utenti Suite Custom Software | DASHBOARD</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<hr>

		<!-- Edit user gestito via Vue (script in edit.js) !-->
		<div id="app">
			<App></App>
		</div>		
		

		<form method='post' action="{{ route('dashboard') }}" id='frm_utenti' name='frm_utenti' autocomplete="off">
			@csrf
 			<input type="hidden" value="{{url('/')}}" id="url" name="url">
			<input type="hidden" name="cur_page" id="cur_page" value="{{$cur_page ?? 0}}">

			<?php
			/*
			admin_lotti:0-1-2
			admin_pns	--->non implementato sul nuovo software: praticamente tutti fanno tutto ma è tutto tracciato ed ognuno può rimuovere la propria documentazione fornita
			ruoli_cert : 1-2-4-5-6-7-10-999
			admin_sos: 0-1-2-10, rst_sos (0-1)
			admin_lp:1-10
			admin_mp: 0-1-10
				PROGRAMMA permessi
					permessi_firma_cr
					permessi_firma_r
					permessi_firma_d
			vest_access (NULL-0-1)
			nc_access  NULL:0-1-2-3-4-5
			-------------------------------------------------
			store e reclami: non li includo in questo pannello
			ruoli_micro (0-1-10-999) ->Aruba
			*/
			?>							
		


			<div class="col-md-12" id='div_elenco'>
				<table id='tb_utenti' class="display">
					<thead>
						<tr>
							<th style='width:200px'>Operazioni</th>
							<th>UserID</th>
							<th>Operatore</th>
							<th>Password</th>
							<th style='width:100px'>Email</th>

							<th>Lotti</th>
							<th>PNS</th>
							<th>Cert</th>
							<th>SOS</th>
							<th>SOS1</th>
							<th>Packing</th>
							<th>MateriePrime</th>
							<th>Vestizione</th>
							<th>NonConformità</th>
						</tr>
					</thead>
					<tbody>

						
						@foreach($utenti as $utente)
							<?php
								$cl="";
								if ($utente->old_pw_for_disable!=null) $cl="canc"; 
							?>	
							<tr id='tr{{$utente->id}}'>
								<td style='width:200px' id='td{{$utente->id}}'>
									
										@if ($cl!="canc")
											@if ($utente->id!="1")
												<button type='button' class="btn btn-warning btn-sm" onclick="disable_user({{$utente->id}})" disabled>
													<i class="fas fa-user-slash"></i> Disabilita
												</button>
											@endif
											
											<button type='button' class="btn btn-success btn-sm" onclick="edit_user({{$utente->id}})" disabled>
												<i class="fas fa-user-cog"></i> Modifica
											</button>


										@else
											<center>
												<button type='button' onclick="enable_user({{$utente->id}})"  class="btn btn-primary btn-sm">
													<i class="fas fa-user-plus"></i> Abilita
												</button>									
											</center>
										@endif
									
								</td>
								<td class="{{$cl}}">
									{{$utente->userid}} 
								</td>
								<td class="{{$cl}}">
									<i>{{$utente->operatore}}</i>
								</td>								
								<td>
									<input type='password' style='background: transparent;color: #000000;border:none;' readonly value='{{$utente->passkey}}'>
								</td>
								<td style='width:100px'>
									{{$utente->email}} 
								</td>

								<td>

								</td>								

								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								
								<td>

								</td>								

							</tr>
						@endforeach
					</tbody>
					
				</table>	

			<?php
				
				$check="";
				if ($view_dele=="1") $check="checked";
			?>

			<div class="row">
			    <div class="col-lg-12">
					<button type="button" class="btn btn-primary" disabled><i class="fa fa-user-plus"></i> Aggiungi utente</button>
					
					<div class="form-check form-switch mt-3 ml-3">
					  <input class="form-check-input" type="checkbox" id="view_dele" name="view_dele" onchange="$('#frm_utenti').submit()" {{ $check }}>
					  <label class="form-check-label" for="view_dele">Mostra utenti disabilitati</label>
					</div>
				</div>
			</div>					
				

					
			</div>
			
			
			
			
			
		
			
			
			<!-- Modal -->
			
			<div class="modal fade bd-example-modal-lg" id="modalvalue" tabindex="-1" role="dialog" aria-labelledby="title_doc" aria-hidden="true">
			  <div class="modal-dialog modal-lg">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="title_doc">Inserimento dati</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body" id='bodyvalue'>
					...
				  </div>
				  <div id='div_wait' class='mb-3'></div>
				  <div class="modal-footer">

					
					<div id='div_save'>
					
					</div>

					<button type="button" class="btn btn-secondary" data-dismiss="modal" id='btn_close'>Chiudi</button>
					
				  </div>
				  
				</div>
			  </div>
			</div>			
		</form>		

				



      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
 @endsection
 
@section('content_plugin')
	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="dist/js/adminlte.min.js"></script>
	
	<!-- inclusione standard
		per personalizzare le dipendenze DataTables in funzione delle opzioni da aggiungere: https://datatables.net/download/
	!-->
	
	<!-- dipendenze DataTables !-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
		 
		 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
		 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		 <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>
	 <!-- fine DataTables !-->	
	
	<script src="{{ URL::asset('/') }}dist/js/dash.js?ver=1.070"></script>
	<script src="{{ URL::asset('/') }}dist/js/edit.js?ver=1.123"></script>
	
	
@endsection
