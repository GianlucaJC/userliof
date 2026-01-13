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

    <title>UserLiof - Dashboard</title>
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
        #tb_utenti {
            font-size: 12px;
            table-layout: fixed;
        }
        .canc {
            color: red !important;
            text-decoration: line-through;
        }
        /* Assicura che anche il testo in corsivo all'interno di .canc sia rosso */
        .canc i {
            color: inherit !important; /* Eredita il colore dal genitore, forzandolo */
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
            <li class="active">
                <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li>
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
                <h1 class="h3 mb-0">Dashboard Gestione Utenti</h1>
                <div class="ms-auto">
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSettings" aria-controls="offcanvasSettings">
                        <i class="fas fa-cog"></i> Impostazioni Globali
                    </button>
                </div>
            </div>
        </nav>

        <!-- User list -->
		<form method='post' action="{{ route('dashboard') }}" id='frm_utenti' name='frm_utenti' autocomplete="off">
			@csrf
 			<input type="hidden" value="{{url('/')}}" id="url" name="url">
			<input type="hidden" name="cur_page" id="cur_page" value="{{$cur_page ?? 0}}">

			<div class="card" id='div_elenco'>
                <div class="card-header">
                    Elenco Utenti
                </div>
                <div class="card-body">
                    <div id="users-table-loader" class="text-center p-5">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Caricamento...</span>
                        </div>
                        <p class="mt-3 mb-0">Caricamento tabella utenti...</p>
                    </div>
                    <div class="table-responsive" style="display: none;">
                        {{-- La tabella è nascosta di default e verrà mostrata da JavaScript una volta che DataTables ha finito di inizializzare. --}}
                        <table id='tb_utenti' class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:210px">Operazioni</th>
                                    <th>UserID</th>
                                    <th>Operatore</th>
                                    <th style="width:150px">Email</th>
                                    <th class="text-center" style="width: 5%;">Interno</th>
                                    <th class="text-center" style="width: 5%;">Esterno</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($combinedUsers as $user)
                                    <tr id="tr{{ $user['id'] }}" class="{{ $user['is_deleted'] ? 'canc' : '' }}">
                                        <td id="td{{ $user['id'] }}">
                                            @if ($user['is_internal'])
                                                @if ($user['is_deleted'])
                                                    <div class="text-center">
                                                        <button type='button' onclick="enable_user({{ $user['id'] }})"  class="btn btn-primary btn-sm">
                                                            <i class="fas fa-user-plus"></i> Abilita
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="d-flex flex-nowrap">
                                                        @if ($user['id'] != "1")
                                                            <button type='button' class="btn btn-warning btn-sm me-1" onclick="disable_user({{ $user['id'] }})" >
                                                                <i class="fas fa-user-slash"></i> Disabilita
                                                            </button>
                                                        @endif
                                                        <button type='button' class="btn btn-success btn-sm" onclick="edit_user({{ $user['id'] }})">
                                                            <i class="fas fa-user-cog"></i> Modifica
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- Utente solo esterno: opzioni per modifica o disabilitazione --}}
                                                <div class="d-flex flex-nowrap">
                                                    <button type='button' class="btn btn-warning btn-sm me-1" onclick="alert('Funzione per disabilitare utente solo esterno da implementare.')" >
                                                        <i class="fas fa-user-slash"></i> Disabilita
                                                    </button>
                                                    <button type='button' class="btn btn-success btn-sm" onclick="edit_external_user('{{ addslashes($user['operatore']) }}', '{{ $user['email'] }}', '{{ $user['userid'] }}')">
                                                        <i class="fas fa-user-cog"></i> Modifica
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $user['userid'] }}</td>
                                        <td><i>{{ $user['operatore'] }}</i></td>
                                        <td>{{ $user['email'] }}</td>
                                        <td class="text-center">
                                            @if ($user['is_internal'])
                                                <i class="fas fa-check-circle text-success" title="Utente presente nel sistema interno"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($user['is_external'])
                                                <i class="fas fa-check-circle text-primary" title="Utente presente nel sistema esterno"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" onclick="add_new_user()"><i class="fas fa-user-plus"></i> Aggiungi utente</button>
                    <div class="form-check form-switch d-inline-block mt-2 ms-3">
                      <input class="form-check-input" type="checkbox" id="view_dele" name="view_dele" onchange="$('#frm_utenti').submit()" {{ ($view_dele ?? '0') == "1" ? "checked" : "" }}>
                      <label class="form-check-label" for="view_dele">Mostra anche utenti disabilitati</label>
                    </div>
                </div>
            </div>

			<!-- Modal -->
			<div class="modal fade" id="modalvalue" tabindex="-1" aria-labelledby="title_doc" aria-hidden="true">
			  <div class="modal-dialog modal-xl">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="title_doc">Modifica Utente e Permessi</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body" id='bodyvalue'>
                      <!-- Vue app for editing, managed by edit.js -->
                      <div id="app"><App></App></div>
				  </div>
				</div>
			  </div>
			</div>
		</form>
    </div>
</div>

<!-- Offcanvas for Global Settings -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSettings" aria-labelledby="offcanvasSettingsLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasSettingsLabel">Impostazioni Globali</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method='post' action="{{ route('dashboard') }}" id='frm_global' name='frm_global' autocomplete="off" class="needs-validation">
            @csrf
            <div class="mb-3">
                <label for="email_notif" class="form-label">Email notifiche edit/view/sign</label>
                <textarea class="form-control" id="email_notif" name='email_notif' rows="3" placeholder='Usare punto e virgola per separare le email'>{{$email_notif ?? ''}}</textarea>
            </div>
            <div class="mb-3">
                <label for="email_notif_green" class="form-label">Email notifiche 'green'</label>
                <textarea class="form-control" id="email_notif_green" name='email_notif_green' rows="3" placeholder='Usare punto e virgola per separare le email'>{{$email_notif_green ?? ''}}</textarea>
            </div>
            <div class="mb-3">
                <label for="codici_esclusi" class="form-label">Codici esclusi</label>
                <textarea class="form-control" id="codici_esclusi" name='codici_esclusi' rows="3" disabled placeholder='Usare punto e virgola per separare i codici'>{{$codici_esclusi ?? ''}}</textarea>
            </div>
            <button type="button" id="btn_save" name="btn_save" value="1" class="btn btn-primary">Salva impostazioni</button>
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
<script src="{{ URL::asset('/') }}dist/js/dash.js?ver=1.085"></script>
<script src="{{ URL::asset('/') }}dist/js/edit.js?ver=1.207"></script>

<script>
    /**
     * Apre la modale per creare un utente completamente nuovo (non presente
     * né nel sistema interno né in quello esterno).
     */
    function add_new_user() {
        try {
            $('#title_doc').text('Aggiungi Nuovo Utente');
            window.moduloEdit.create_new(); // Chiama il nuovo metodo in Vue
            $('#modalvalue').modal('show');
        } catch (e) {
            console.error("Errore in add_new_user: ", e);
            Swal.fire('Errore Applicazione', 'Impossibile inizializzare il form di creazione. Controllare la console per i dettagli.', 'error');
        }
    }

    /**
     * Apre la modale per modificare un utente. 
     * Per gli utenti solo esterni, pre-compila i campi con i dati noti
     * e carica i permessi dal sistema esterno.
     * Da questo form è possibile sia modificare solo i dati esterni, sia creare
     * il corrispettivo utente interno fornendo una password e permessi interni.
     */
    function edit_external_user(operatore, email, userid) {
        try {
            // Titolo generico perché da qui si può sia modificare l'utente esterno
            // sia creare il corrispettivo interno.
            $('#title_doc').text('Modifica Utente');
    
            const userData = {
                name: operatore,
                email: email,
                username: userid
            };
    
            window.moduloEdit.load_info(userData);
            $('#modalvalue').modal('show');
        } catch (e) {
            console.error("Errore in edit_external_user: ", e);
            Swal.fire('Errore Applicazione', 'Impossibile inizializzare il form di modifica. Controllare la console per i dettagli.', 'error');
        }
    }

    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });

    // Script per gestire la visualizzazione della tabella utenti con loader
    (function($) {
        var $loader = $('#users-table-loader');
        var $container = $('#tb_utenti').closest('.table-responsive');
        var startTime = new Date().getTime();
        var minDisplayTime = 400; // Ritardo minimo in millisecondi per mostrare lo spinner.

        // Funzione per nascondere il loader e mostrare la tabella, rispettando il tempo minimo
        var completeLoading = function() {
            var elapsedTime = new Date().getTime() - startTime;
            var timeToShow = minDisplayTime - elapsedTime;

            if (timeToShow < 0) {
                timeToShow = 0;
            }

            setTimeout(function() {
                $loader.hide();
                $container.show();
            }, timeToShow);
        };

        var checkInterval = setInterval(function() {
            if ($('#tb_utenti').hasClass('dataTable')) {
                clearInterval(checkInterval);
                checkInterval = null; // Pulisce la variabile per il timeout di sicurezza
                completeLoading();
            }
        }, 100); // Controlla ogni 100ms

        setTimeout(function() {
            if (checkInterval) { // Se il controllo è ancora attivo dopo 5 secondi (timeout di sicurezza)
                clearInterval(checkInterval);
                $loader.hide();
                $container.show(); // Forza la visualizzazione per evitare blocchi
            }
        }, 5000);
    })(jQuery);
</script>

</body>
</html>
