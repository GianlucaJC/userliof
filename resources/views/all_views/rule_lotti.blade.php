<?php
use App\Models\User;
	$id = Auth::user()->id;
	$user = User::find($id);
	

?>
@extends('all_views.viewmaster.index')

@section('title', 'UserLiof')

@section('extra_style') 
	<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
@endsection
<style>

</style>



@section('operazioni')
@endsection


@section('notifiche') 
@endsection


@section('content_main')

  <!-- Content Wrapper. Contains page content -->


  
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">UserLiof - Regole Lotti</h1>
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


		<form method='post' action="{{ route('rule_lotti') }}" id='frm_rule' name='frm_rule' autocomplete="off">
			@csrf
            <input type="hidden" value="{{url('/')}}" id="url" name="url">
            
            <div class="col-md-12" id='div_elenco'>


              
              <div id='div_resp_test' style='display:none'>
              </div>

              <div class="input-group mt-3 mb-3" style='width:60%'>
              <button type='button' class="btn btn-primary btn-sm" onclick="new_rule()">
                <i class="fas fa-plus-square"></i> Crea nuova regola
              </button>                
               
              <input type="text" class="ml-5 form-control" placeholder="Codice da testare" aria-label="Codice da testare" id='test_codice'>
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" onclick="testcodice()">
                    <i class="fas fa-keyboard"></i> Inserisci un codice e vedi la regola applicata (o conflitti di regole)
                    </button>
                  </div>
              </div> 

              <div class='refr mb-3' id='div_refrnew' style='display:none'>
                    <button type='submit' class="mt-2 btn btn-primary btn-sm" >
                      <i class="fas fa-sync"></i> Refresh necessario (dopo creazione regola)
                  </button>
              </div>  



              <table id='tb_regole' class="display">
                <thead>
                  <tr>
                    <th style='width:180px'>Operazioni</th>
                    <th>IDregola</th>
                    <th>Pattern</th>
                    <th>Inizia con...</th>
                    <th>MinLen</th>
                    <th>MaxLen</th>
                    <th>Len</th>
                    <th>DBmodelli</th>
                    <th>DBmodelli1</th>
                    <th>Creata</th>
                    <th>Modificata</th>
                  </tr>
                </thead>
                <tbody>

                  
                  @foreach($regole as $regola)
                    <tr id='tr{{$regola->id}}' class='allrow'>
                      <td style='width:200px' id='td{{$regola->id}}'>

                      <span id='id_regola{{$regola->id}}' 
                        data-pattern='{{$regola->pattern}}'
                        
                        
                        data-inizia_con='{{$regola->inizia_con}}'
                        data-min_len='{{$regola->min_len}}'
                        data-max_len='{{$regola->max_len}}'
                        data-len='{{$regola->len}}'

                        data-inidbmod='{{$regola->DBmodelli}}'
                        data-inidbmod1='{{$regola->DBmodelli1}}'
                      ></span>
                      

                          <button type='button' class="btn btn-success btn-sm" onclick="edit_rule({{$regola->id}})">
                           <i class="fas fa-edit"></i> Modifica
                          </button>

                          <button type='button' class="ml-2 btn btn-warning btn-sm elimina" onclick="dele_rule({{$regola->id}})">
                           <i class="fas fa-trash"></i> Elimina
                          </button>                          
                          <div class='refr' id='div_refr{{$regola->id}}' style='display:none'>
                            <button type='submit' class="mt-2 btn btn-primary btn-sm" >
                              <i class="fas fa-sync"></i> Refresh necessario (dopo la tua modifica)
                            </button 
                          </div> 
                      </td>
                      <td>
                        {{$regola->id}} 
                      </td>                      
                      <td>
                        {{$regola->pattern}} 
                      </td>
                      <td>
                        {{$regola->inizia_con}}
                      </td>								
                      <td>
                        {{$regola->min_len}}
                      </td>				
                      <td>
                        {{$regola->max_len}}
                      </td>
                      <td>
                        {{$regola->len}}
                      </td>
                      <td>
                        @if ($regola->DBmodelli=="[codice,0].ciff")
                          Codice (dal 1° carattere in poi).ciff
                        @elseif ($regola->DBmodelli=="[codice,1].ciff")
                          Codice (dal 2° carattere in poi).ciff
                        @else
                          {{$regola->DBmodelli}}
                        @endif  
                      </td>
                      <td>
                        {{$regola->DBmodelli1}}
                      </td>                  
                      <td>
                        {{$regola->created_at}}
                      </td>                 
                      <td>
                        {{$regola->updated_at}}
                      </td>                              
                    @endforeach
                </tbody>
                
              </table>
              
             

            </div>	


            <div id="app">
                <!-- Vue.js components will be processed here. -->
             
                <regole-a food-name="A3"></regole-a>
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

   <script src="{{ URL::asset('/') }}dist/js/rule.js?ver=1.154"></script>
	
	
@endsection
