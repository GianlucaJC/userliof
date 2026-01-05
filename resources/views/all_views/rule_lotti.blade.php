<?php
use App\Models\User;
	$id = Auth::user()->id;
	$user = User::find($id);
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>UserLiof - Regole Lotti</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='%23007bff' d='M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4-11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1-9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.4-.6-11.2-5.5-14zm-231.8-114c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z'/></svg>" type="image/svg+xml">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #f8f9fa;
            color: #333;
            transition: all 0.3s;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #e9ecef;
            text-align: center;
        }
        #sidebar .sidebar-header h3 {
            color: #495057;
            font-weight: 600;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul p {
            color: #6c757d;
            padding: 10px;
        }
        #sidebar ul li a {
            padding: 10px;
            font-size: 1.1em;
            display: block;
            color: #495057;
            text-decoration: none;
        }
        #sidebar ul li a:hover {
            color: #000;
            background: #e9ecef;
        }
        #sidebar ul li.active > a, a[aria-expanded="true"] {
            color: #fff;
            background: #007bff;
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        #tb_regole {
            font-size: 12px;
        }
        td {
            word-wrap: break-word;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-users-cog me-2"></i>UserLiof</h3>
        </div>

        <ul class="list-unstyled components">
            <p>Menu Principale</p>
            <li>
                <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="active">
                <a href="{{ route('rule_lotti') }}"><i class="fas fa-file-alt me-2"></i> Regole Lotti</a>
            </li>
            <!-- Aggiungi qui altri link per il menu -->
            <hr>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-secondary d-md-none">
                    <i class="fas fa-align-left"></i>
                </button>
                <h1 class="h3 mb-0">Gestione Regole Lotti</h1>
            </div>
        </nav>

        <form method='post' action="{{ route('rule_lotti') }}" id='frm_rule' name='frm_rule' autocomplete="off">
            @csrf
            <input type="hidden" value="{{url('/')}}" id="url" name="url">
            
            <div class="col-md-12" id='div_elenco'>
              <div id='div_resp_test' style='display:none'></div>

              <div class="input-group mt-3 mb-3" style='width:60%'>
                  <button type='button' class="btn btn-primary btn-sm" onclick="new_rule()">
                    <i class="fas fa-plus-square"></i> Crea nuova regola
                  </button>                
                  <input type="text" class="ms-5 form-control" placeholder="Codice da testare" aria-label="Codice da testare" id='test_codice'>
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" onclick="testcodice()">
                        <i class="fas fa-keyboard"></i> Test Codice
                    </button>
                  </div>
              </div> 

              <div class='refr mb-3' id='div_refrnew' style='display:none'>
                    <button type='submit' class="mt-2 btn btn-primary btn-sm" >
                      <i class="fas fa-sync"></i> Refresh necessario (dopo creazione regola)
                    </button>
              </div>  

              <table id='tb_regole' class="display table table-striped table-hover" style="width:100%">
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
                          <button type='button' class="ms-2 btn btn-warning btn-sm elimina" onclick="dele_rule({{$regola->id}})">
                           <i class="fas fa-trash"></i> Elimina
                          </button>                          
                          <div class='refr' id='div_refr{{$regola->id}}' style='display:none'>
                            <button type='submit' class="mt-2 btn btn-primary btn-sm" >
                              <i class="fas fa-sync"></i> Refresh necessario (dopo la tua modifica)
                            </button> 
                          </div> 
                      </td>
                      <td>{{$regola->id}}</td>                      
                      <td>{{$regola->pattern}}</td>
                      <td>{{$regola->inizia_con}}</td>								
                      <td>{{$regola->min_len}}</td>				
                      <td>{{$regola->max_len}}</td>
                      <td>{{$regola->len}}</td>
                      <td>
                        @if ($regola->DBmodelli=="[codice,0].ciff")
                          Codice (dal 1° carattere in poi).ciff
                        @elseif ($regola->DBmodelli=="[codice,1].ciff")
                          Codice (dal 2° carattere in poi).ciff
                        @else
                          {{$regola->DBmodelli}}
                        @endif  
                      </td>
                      <td>{{$regola->DBmodelli1}}</td>                  
                      <td>{{$regola->created_at}}</td>                 
                      <td>{{$regola->updated_at}}</td>                              
                    @endforeach
                </tbody>
              </table>
            </div>	

            <div id="app">
                <regole-a></regole-a>
            </div>
        </form>		
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables & Plugins -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>

<!-- SweetAlert2 -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom App Scripts -->
<script src="{{ URL::asset('/') }}dist/js/rule.js?ver=1.158"></script>

<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>

</body>
</html>
