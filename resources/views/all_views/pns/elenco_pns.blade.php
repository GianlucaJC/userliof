@extends('all_views.viewmaster.index')

@section('title', 'PNS')
@section('extra_style') 
<!-- x button export -->

<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
<!-- -->

 <!-- per upload -->
  <link href="{{ URL::asset('/') }}dist/css/upload/jquery.dm-uploader.min.css" rel="stylesheet">
  <!-- per upload -->  
  <link href="{{ URL::asset('/') }}dist/css/upload/styles.css?ver=1.1" rel="stylesheet">  
@endsection



<style>
	tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>
@section('content_main')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">ELENCO PNS</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
			  <li class="breadcrumb-item">Main Menu</li>
              <li class="breadcrumb-item active">Elenco Pns</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<form method='post' action="{{ route('elenco_pns') }}" id='frm_pns' name='frm_pns' autocomplete="off">
			<input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>
			<input type="hidden" value="{{url('/')}}" id="url" name="url">

		<?php
			$vtab="display:block";
			$vlog="display:none";
			if (count($view_log)!=0) {
				$vtab="display:none";
				$vlog="display:block";
			}
		?>
        <div id='vlog' class="row" style="{{$vlog}}">
			<div class="col-md-12">
				<table id='tbl_log' class="display">
					<thead>
						<tr>
							<th style='width:40px'>ID</th>
							<th>User</th>
							<th>Operazione</th>
							<th>Modulo</th>
							<th>Dettaglio</th>
							<th>Data operazione</th>
						</tr>
					</thead>
					<tbody>
						@foreach($view_log as $log)
							<tr>
								<td style='width:40px'>
									{{$log->id}}
									
								</td>
								<td>
									{{$log->user}} 
								</td>
								<td>
									{{$log->operazione}} 
								</td>
								<td>
									{{$log->modulo}} 
								</td>
								<td>
									{{$log->dettaglio}} 
								</td>
								<td>
									{{$log->created_at}} 
								</td>
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th style='width:40px'>ID</th>
							<th>User</th>
							<th>Operazione</th>
							<th>Modulo</th>
							<th>Dettaglio</th>
							<th>Data operazione</th>
						</tr>
					</tfoot>					
				</table>					

			</div>
			
			
			<button type="submit" class="btn btn-primary">Chiudi LOG</button>
			
		</div>	
			
			
		<input type="hidden" name="cur_page" id="cur_page" value="{{$cur_page ?? 0}}">	
		<div id='vtab' style='{{$vtab}}'>	
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-warning" role="alert" id='div_alert' style='display:none'>
						
					</div>
				</div>
				<div class="p-2">
					<p class="btn btn-outline-primary float-right" onclick="$('.firme').toggle(200)">
						Visualizza/Nascondi Firme
					</p>
				</div>

				<div class="col-md-12">
					
					<table id='tbl_pns' class="display">
						<thead>
							<tr>
								<th style='width:70px'>Codice</th>
								<th style='width:40px'>Edit/View</th>
								<th style='width:40px'>Status</th>
								<th>Descrizione</th>
								<th style='width:120px'>Data creazione</th>
								<th style='width:120px'>LastUpdate</th>
								<th style='width:70px'>IVD</th>
								<th style='min-width:400px'>Operazioni</th>
							</tr>
						</thead>
						<tbody>
							@include("all_views.pns.tb_pns")
						</tbody>
						<tfoot>
							<tr>
								<th style='width:40px'></th>
								<th style='width:40px'>status</th>
								<th style='width:70px'>Codice</th>
								<th>Descrizione</th>
								<th style='width:120px'>Data creazione</th>
								<th style='width:120px'>LastUpdate</th>
								<th style='width:70px'>IVD</th>
								<th style='min-width:400px'></th>
							</tr>
						</tfoot>					
					</table>
					<input type='hidden' id='dele_contr' name='dele_contr'>
					<input type='hidden' id='restore_contr' name='restore_contr'>
					<input type='hidden' id='log_id' name='log_id'>
				
			  </div>

			</div>
			<?php
			
				$check="";
				if ($view_dele=="1") $check="checked";
			?>
			

			<div class="row">
				<div class="col-lg-12">


					<div class="form-check form-switch mt-3 ml-3">
					  <input class="form-check-input" type="checkbox" id="view_dele" name="view_dele" onchange="$('#frm_pns').submit()" {{ $check }}>
					  <label class="form-check-label" for="view_dele">Mostra anche elementi eliminati</label>
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
        <!-- /.row -->
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

	<!-- per upload -->
	<script src="{{ URL::asset('/') }}dist/js/upload/jquery.dm-uploader.min.js"></script>
	<script src="{{ URL::asset('/') }}dist/js/upload/demo-ui.js?ver=1.311"></script>
	<script src="{{ URL::asset('/') }}dist/js/upload/demo-config.js?ver=2.415"></script>	

	
	<!-- inclusione standard
		per personalizzare le dipendenze DataTables in funzione delle opzioni da aggiungere: https://datatables.net/download/
	!-->
	
	<!-- dipendenze DataTables !-->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
		 
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>
	<!-- fine DataTables !-->
	
	

	<script src="{{ URL::asset('/') }}dist/js/elenco_pns.js?ver=1.176"></script>

@endsection