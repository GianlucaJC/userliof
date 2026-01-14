<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


use App\Models\utenti;

use DB;
use Mail;


class mainController extends Controller
{
public function __construct()
	{
			
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
			
		
		if (!Auth::user()) {
				//non riesco ad invocare il logout

		} else  
			$id_user=Auth::user()->id;
		
		$this->middleware('auth')->except(['index']);

	}	

	public function mail_notif($id_pns,$from) {
		$status=array();
		
		$db_set = db_set::find(1);
		if (!isset($db_set)) return;
		if ($from==1 || $from==2)
			$info=$db_set->email_notif;
		if ($from==3) 
			$info=$db_set->email_notif_green;
		else return false;
		$emails=explode(";",$info);
		for ($sca=0;$sca<=count($emails)-1;$sca++) {			
			$email=$emails[$sca];
			if (strlen($email)==0) continue;
			try {
				$msg="";
				$data["email"] = $email;
				$data["title"] = "Alert PNS";
				if ($from==1)
					$msg = "E' stata effettuata una modifica sul PNS\n\n";
				if ($from==2)
					$msg = "E' stata apposta la firma QA sulla scheda prodotto PNS\n\n";
				if ($from==3)
					$msg = "Il PNS è concluso\n\n";


				//$prefix="http://localhost:8012";
				$prefix="http://liojls02.ad.liofilchem.net:8012";
				$lnk=$prefix."/pns/public/recensione/$id_pns";

				$msg.="\nCliccare quì $lnk per i dettagli sul PNS";
				
				$data["body"]=$msg;
				

				Mail::send('emails.notifdoc', $data, function($message)use($data) {
					$message->to($data["email"], $data["email"])
					->subject($data["title"]);

				});
				
				$status[$sca]['status']="OK";
				$status[$sca]['message']="Mail $email inviata con successo";
				
				
				
			} catch (Throwable $e) {
				$status[$sca]['status']="KO";
				$status[$sca]['message']="Errore $email occorso durante l'invio! $e";
			}
		}
	}
	

	
	
	public function dashboard(Request $request) {

		$view_dele=$request->input("view_dele");
		if (strlen($view_dele)==0) $view_dele=0;
		if ($view_dele=="on") $view_dele=1;
		
		
		// 1. Recupera utenti locali
		$localUsersQuery = DB::table('utenti');
		if ($view_dele != "1") {
			$localUsersQuery->where('attivo', 1);
		}
		$localUsers = $localUsersQuery->get();

		// 2. Recupera utenti remoti
		$remoteUsers = [];
		$externalServiceAvailable = true;
		// Per maggiore sicurezza, questo token andrebbe memorizzato nel file .env
        $apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';
		try {
			// Usando il facade Http di Laravel
			$response = Http::timeout(5)->asForm()->post('https://www.liofilchemstore.it/servizi/api_login_ext.php', [
                'api_token' => $apiToken,
                'action' => 'get_users'
            ]);
			if ($response->successful()) {
				$data = $response->json();
				if (isset($data['status']) && $data['status'] === 'ok') {
					$remoteUsers = $data['data'];
				} else {
					$externalServiceAvailable = false;
					Log::error("API esterna (utenti) ha risposto con un errore: " . ($data['message'] ?? 'Errore sconosciuto'));
				}
			} else {
				$externalServiceAvailable = false;
				Log::error("API esterna (utenti) non raggiungibile o ha restituito un errore HTTP: " . $response->status());
			}
		} catch (\Exception $e) {
			$externalServiceAvailable = false;
			Log::error("Impossibile connettersi all'API esterna (utenti): " . $e->getMessage());
		}

		// 3. Mappa gli utenti per un accesso rapido tramite userid (in modo case-insensitive e trimmando gli spazi)
		$localUserMap = $localUsers->keyBy(function ($item) {
			return strtolower(trim($item->userid));
		});
		$remoteUserMap = collect($remoteUsers)->keyBy(function ($item) {
			return isset($item['userid']) ? strtolower(trim($item['userid'])) : null;
		})->filter(); // Rimuove eventuali chiavi nulle

		// 4. Unisci le liste creando un elenco master di userid
		$allUserIds = $localUserMap->keys()->merge($remoteUserMap->keys())->unique();

		// 5. Costruisci la lista finale
		$combinedUsers = [];
		foreach ($allUserIds as $userId) {
			if (empty($userId)) continue; // Salta eventuali userid vuoti (già gestito da ->filter() su remote)
			
			// Usa la stessa chiave normalizzata per la ricerca
			$local = $localUserMap->get($userId); // $userId è già minuscolo
			$remote = $remoteUserMap->get($userId); // $userId è già minuscolo

			$combinedUsers[] = [
				'id'          => $local->id ?? null,
				// Per la visualizzazione, usa lo userid con la formattazione originale, dando priorità a quello locale
				'userid'      => $local->userid ?? ($remote['userid'] ?? $userId),
				'operatore'   => $local->operatore ?? ($remote['operatore'] ?? 'N/D'),
				'email'       => $local->email ?? '',
				'is_internal' => $local !== null,
				'is_external' => $remote !== null,
				'is_deleted'  => $local ? ($local->attivo == 0) : false,
			];
		}

		// Ordina la lista finale per nome operatore
		usort($combinedUsers, function ($a, $b) {
			return strcasecmp($a['operatore'], $b['operatore']);
		});
		
		// 6. Passa la lista unificata alla vista
		return view('all_views/dashboard',compact('combinedUsers','view_dele', 'externalServiceAvailable'));
	
	}
	
	public function load_info(Request $request) {
		$id_user = $request->input('id_user');
		$user = utenti::find($id_user);
		if (!$user) {
			return response()->json([]);
		}

		// Check if user exists externally
		$is_external = false;
		$apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';
		try {
			$response = Http::timeout(3)->asForm()->post('https://www.liofilchemstore.it/servizi/api_login_ext.php', [
				'api_token' => $apiToken,
				'action' => 'get_user_permissions', // This action checks for user existence
				'userid' => $user->userid
			]);
			if ($response->successful()) {
				$data = $response->json();
				if (isset($data['status']) && $data['status'] === 'ok') {
					$is_external = true;
				}
			}
		} catch (\Exception $e) {
			// Ignore, assume not external if service fails
			Log::warning("load_info: Impossibile verificare l'esistenza esterna per userid {$user->userid}: " . $e->getMessage());
		}

		$userData = $user->toArray();
		$userData['is_external'] = $is_external;
		$userData['is_internal'] = true; // By definition, since we found it

		return response()->json([$userData]);
	}	
	
	public function disable_user(Request $request) {
        $id_user = $request->input('id_user');
        $userid_from_request = $request->input('userid');

        // Caso 1: Utente interno (o interno+esterno), identificato da id_user
        if ($id_user) {
            $user = utenti::find($id_user);
            if (!$user) {
                return response()->json(['response' => 'KO', 'message' => 'Utente locale non trovato.'], 404);
            }
    
            // 1. Disabilita utente locale
            utenti::where('id', $id_user)->update(['attivo' => 0, 'ruoli_cert' => 999]);
    
            // 2. Tenta di disabilitare l'utente esterno, se esiste
            $syncResult = $this->syncExternalUserStatus($user->userid, 'disable_user');
    
            if ($syncResult['success']) {
                return response()->json(['response' => 'OK', 'message' => 'Utente disabilitato con successo in locale e in esterno.']);
            } else {
                return response()->json([
                    'response' => 'PARTIAL_OK',
                    'message' => 'Utente disabilitato localmente, ma si è verificato un problema con la sincronizzazione esterna: ' . $syncResult['message']
                ]);
            }
        } 
        // Caso 2: Utente solo esterno, identificato da userid
        else if ($userid_from_request) {
            // Tenta di disabilitare solo l'utente esterno
            $syncResult = $this->syncExternalUserStatus($userid_from_request, 'disable_user');

            if ($syncResult['success']) {
                return response()->json(['response' => 'OK', 'message' => 'Utente esterno disabilitato con successo.']);
            } else {
                return response()->json([
                    'response' => 'KO',
                    'message' => 'Si è verificato un problema durante la disabilitazione dell\'utente esterno: ' . $syncResult['message']
                ], 500);
            }
        }
        // Caso 3: Nessun identificativo fornito
        else {
            return response()->json(['response' => 'KO', 'message' => 'Identificativo utente (id_user o userid) non fornito.'], 400);
        }
	}	

	public function enable_user(Request $request) {
        $id_user = $request->input('id_user');
        $userid_from_request = $request->input('userid');

        // Caso 1: Utente interno (o interno+esterno), identificato da id_user
        if ($id_user) {
            $user = utenti::find($id_user);
            if (!$user) {
                return response()->json(['response' => 'KO', 'message' => 'Utente locale non trovato.'], 404);
            }

            // 1. Abilita utente locale
            utenti::where('id', $id_user)->update(['attivo' => 1]);

            // 2. Tenta di abilitare l'utente esterno
            $syncResult = $this->syncExternalUserStatus($user->userid, 'enable_user');

            if ($syncResult['success']) {
                return response()->json(['response' => 'OK', 'message' => 'Utente abilitato con successo in locale e in esterno.']);
            } else {
                return response()->json([
                    'response' => 'PARTIAL_OK',
                    'message' => 'Utente abilitato localmente, ma si è verificato un problema con la sincronizzazione esterna: ' . $syncResult['message']
                ]);
            }
        }
        // Caso 2: Utente solo esterno, identificato da userid
        else if ($userid_from_request) {
            // Tenta di abilitare solo l'utente esterno
            $syncResult = $this->syncExternalUserStatus($userid_from_request, 'enable_user');

            if ($syncResult['success']) {
                return response()->json(['response' => 'OK', 'message' => 'Utente esterno abilitato con successo.']);
            } else {
                return response()->json([
                    'response' => 'KO',
                    'message' => 'Si è verificato un problema durante l\'abilitazione dell\'utente esterno: ' . $syncResult['message']
                ], 500);
            }
        }
        // Caso 3: Nessun identificativo fornito
        else {
            return response()->json(['response' => 'KO', 'message' => 'Identificativo utente (id_user o userid) non fornito.'], 400);
        }
	}	

	/**
     * Gestisce la creazione e l'aggiornamento di un utente in base agli switch
     * per il sistema interno ed esterno.
     */
    public function update_user(Request $request)
    {
        // 1. VALIDAZIONE DI BASE
		$validator = Validator::make($request->all(), [
			'id_user' => 'nullable|integer',
			'operatore' => 'required|string|max:30',
			'userid' => 'required|string|max:15',
			'email' => 'nullable|email|max:120',
			'password' => 'nullable|string|max:15',
			'wants_to_create_internal' => 'required|boolean',
			'wants_to_sync_external' => 'required|boolean',
			'admin_lotti' => 'nullable|integer|in:0,1,9',
			'ruoli_cert' => 'nullable|integer|in:1,2,4,5,6,7,10,999',
			'admin_sos' => 'nullable|integer|in:0,1,2,10,9',
			'rst_sos' => 'nullable|integer|in:0,1',
			'admin_lp' => 'nullable|integer|in:1,10',
			'admin_mp' => 'nullable|integer|in:0,1,10',
			'vest_access' => 'nullable|string|in:"","0","1"',
			'nc_access' => 'nullable|integer|in:0,1,2,3,4,5',
			'permessi_firma_cr' => 'nullable|integer|in:0,1',
			'permessi_firma_r' => 'nullable|integer|in:0,1',
			'permessi_firma_d' => 'nullable|integer|in:0,1',
			'permessi_reparti' => 'nullable|array',
			'permessi_reparti.*' => 'integer',
		]);

		if ($validator->fails()) {
			return response()->json(['response' => 'KO', 'message' => $validator->errors()->first()], 422);
		}

		$data = $validator->validated();

		if (!$data['wants_to_create_internal'] && !$data['wants_to_sync_external']) {
			return response()->json(['response' => 'KO', 'message' => 'Selezionare se creare/abilitare l\'utente nel sistema interno, esterno o entrambi.'], 422);
		}

		$user = null;
		$internalSaveSuccess = null; // null: non tentato, true: successo, false: fallito
		$externalSyncSuccess = null; // null: non tentato, true: successo, false: fallito
		$externalSyncMessage = '';

		// 2. GESTIONE UTENTE INTERNO
		if ($data['wants_to_create_internal']) {
			$userId = $data['id_user'];
			$isNewInternalUser = empty($userId);

			$internalValidator = Validator::make($data, [
				'userid' => $isNewInternalUser
					? Rule::unique('utenti', 'userid')
					: Rule::unique('utenti', 'userid')->ignore($userId),
				'password' => 'required|string|max:15',
			]);

			if ($internalValidator->fails()) {
				return response()->json(['response' => 'KO', 'message' => "Errore dati utente interno: " . $internalValidator->errors()->first()], 422);
			}

			try {
				if ($isNewInternalUser) {
					$user = new utenti();
				} else {
					$user = utenti::find($userId);
					if (!$user) {
						return response()->json(['response' => 'KO', 'message' => 'Utente interno da modificare non trovato.'], 404);
					}
				}

				$user->operatore = $data['operatore'];
				$user->userid = $data['userid'];
				$user->email = $data['email'];
				$user->password_hash = Hash::make($data['password']);
				$user->passkey = $data['password'];
				$user->attivo = 1;
				$user->admin_lotti = $request->input('admin_lotti') ?? 9;
				$user->ruoli_cert = $request->input('ruoli_cert') ?? 999;
				$user->admin_sos = $request->input('admin_sos') ?? 9;
				$user->rst_sos = $request->input('rst_sos') ?? 0;
				$user->admin_lp = $request->input('admin_lp') ?? 10;
				$user->admin_mp = $request->input('admin_mp') ?? 0;
				$user->nc_access = $request->input('nc_access') ?? 0;
				$vest_access_value = $request->input('vest_access');
				$user->vest_access = ($vest_access_value === '' || $vest_access_value === null) ? null : $vest_access_value;

				$user->timestamps = false;
				$user->save();
				$internalSaveSuccess = true;
			} catch (\Exception $e) {
				Log::error("Errore salvataggio utente interno: " . $e->getMessage());
				$internalSaveSuccess = false;
			}
		}

		// 3. GESTIONE UTENTE ESTERNO
		if ($data['wants_to_sync_external']) {
			if (empty($data['password'])) {
				return response()->json(['response' => 'KO', 'message' => 'La password è richiesta per la sincronizzazione con il sistema esterno.'], 422);
			}

			$userForApi = $user;
			if (!$userForApi) {
				$userForApi = new utenti();
				$userForApi->operatore = $data['operatore'];
				$userForApi->userid = $data['userid'];
			}
			$userForApi->passkey = $data['password'];

			$externalResponse = $this->syncWithExternalApi($userForApi, $request);
			if ($externalResponse['status'] === 'ok') {
				$externalSyncSuccess = true;
			} else {
				$externalSyncSuccess = false;
				$externalSyncMessage = $externalResponse['message'];
			}
		}

		// 4. RISPOSTA FINALE
		$messages = [];
		$isError = false;

		if ($internalSaveSuccess === true) {
			$messages[] = 'Utente interno salvato con successo.';
		} elseif ($internalSaveSuccess === false) {
			$messages[] = 'Salvataggio utente interno fallito.';
			$isError = true;
		}

		if ($externalSyncSuccess === true) {
			$messages[] = 'Sincronizzazione esterna completata.';
		} elseif ($externalSyncSuccess === false) {
			$messages[] = 'Sincronizzazione esterna fallita: ' . $externalSyncMessage;
			$isError = true;
		}

		if (empty($messages)) {
			return response()->json(['response' => 'KO', 'message' => 'Nessuna operazione eseguita.'], 400);
		}

		$finalStatus = 'OK';
		if ($isError) {
			$finalStatus = ($internalSaveSuccess !== null && $externalSyncSuccess !== null) ? 'PARTIAL_OK' : 'KO';
		}

		return response()->json([
			'response' => $finalStatus,
			'message' => implode(' ', $messages),
			'user' => $user ? $user->fresh() : null
		]);
    }

    /**
     * Funzione helper per comunicare con l'API esterna.
     */
    private function syncWithExternalApi(utenti $user, Request $request)
    {
        // NOTA: Il token dovrebbe essere in un file .env per sicurezza.
        $apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';
        $apiUrl = 'https://www.liofilchemstore.it/servizi/api_login_ext.php';

        $repartoString = null;
        if ($request->has('permessi_reparti') && is_array($request->input('permessi_reparti'))) {
            $repartoString = collect($request->input('permessi_reparti'))
                ->map(function ($id) {
                    return "R{$id}R";
                })
                ->implode(';');
        }


        $response = Http::asForm()->post($apiUrl, [
            'api_token' => $apiToken,
            'action' => 'update_permissions',
            'userid' => $user->userid,
            'operatore' => $user->operatore,
            'passkey' => $user->passkey, // Invia la password in chiaro come richiesto dall'API esterna
            'permessi_firma_cr' => $request->input('permessi_firma_cr', 0),
            'permessi_firma_r' => $request->input('permessi_firma_r', 0),
            'permessi_firma_d' => $request->input('permessi_firma_d', 0),
            'reparto' => $repartoString,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            // L'API esterna risponde con 'ok' sia per l'update che per l'insert
            if (isset($body['status']) && $body['status'] === 'ok') {
                return ['status' => 'ok', 'message' => $body['message'] ?? 'Sincronizzazione completata.'];
            }
            return ['status' => 'error', 'message' => $body['message'] ?? 'Errore sconosciuto dall\'API esterna.'];
        }

        return ['status' => 'error', 'message' => 'Errore di connessione con l\'API esterna (HTTP ' . $response->status() . ').'];
    }

    private function syncExternalUserStatus($userId, $action)
    {
        // $action può essere 'disable_user' o 'enable_user'
        $apiUrl = 'https://www.liofilchemstore.it/servizi/api_login_ext.php';
        $apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';

        try {
            $response = Http::asForm()->post($apiUrl, [
                'api_token' => $apiToken,
                'action' => $action,
                'userid' => $userId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // L'API esterna risponde 'ok' anche se l'utente non esiste, che è il comportamento desiderato.
                if (isset($data['status']) && $data['status'] === 'ok') {
                    return ['success' => true, 'message' => $data['message'] ?? 'Operazione esterna completata.'];
                } else {
                    $errorMessage = $data['message'] ?? 'L\'API esterna ha restituito un errore non gestito.';
                    Log::warning("Sincronizzazione stato utente ($action) per userid $userId fallita (API Error): $errorMessage");
                    return ['success' => false, 'message' => $errorMessage];
                }
            } else {
                Log::warning("Sincronizzazione stato utente ($action) per userid $userId fallita (HTTP Error): " . $response->status());
                return ['success' => false, 'message' => 'Il servizio esterno non è raggiungibile (Errore HTTP ' . $response->status() . ').'];
            }
        } catch (\Exception $e) {
            Log::warning("Sincronizzazione stato utente ($action) per userid $userId fallita (Exception): " . $e->getMessage());
            return ['success' => false, 'message' => 'Errore di connessione con il servizio esterno.'];
        }
    }

    private function checkExternalUserExists($userId)
    {
        $apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';
        try {
            $response = Http::timeout(3)->asForm()->post('https://www.liofilchemstore.it/servizi/api_login_ext.php', [
                'api_token' => $apiToken,
                'action' => 'get_user_permissions', // Usiamo questa azione che fallisce se l'utente non esiste
                'userid' => $userId
            ]);
            if ($response->successful()) {
                $data = $response->json();
                // Se lo status è 'ok', l'utente esiste
                if (isset($data['status']) && $data['status'] === 'ok') {
                    return ['exists' => true, 'error' => false];
                }
            }
            // In tutti gli altri casi (risposta non 200, status 'error', etc.), l'utente non esiste o non è verificabile
            return ['exists' => false, 'error' => false];
        } catch (\Exception $e) {
            Log::warning("checkExternalUserExists: Impossibile verificare l'esistenza esterna per userid {$userId}: " . $e->getMessage());
            // C'è stato un errore di connessione, non possiamo saperlo
            return ['exists' => false, 'error' => true, 'message' => $e->getMessage()];
        }
    }
}
