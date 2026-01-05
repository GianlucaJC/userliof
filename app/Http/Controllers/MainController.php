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


		$email_notif="";
		$email_notif_green="";
		$codici_esclusi="";

		$view_dele=$request->input("view_dele");
		if (strlen($view_dele)==0) $view_dele=0;
		if ($view_dele=="on") $view_dele=1;
		
		
		$utenti=DB::table('utenti')
		->when($view_dele != "1", function ($query) {
			return $query->where('attivo', 1);
		})
		->get();		

		return view('all_views/dashboard',compact('email_notif','email_notif_green','codici_esclusi','utenti','view_dele'));
	
	}
	
	public function load_info(Request $request) {
		$id_user = $request->input('id_user');
		$user=utenti::find($id_user);
		return response()->json($user ? [$user] : []);
	}	
	
	public function disable_user(Request $request) {
		$id = $request->input('id_user');
		//'old_pw_for_disable' => DB::raw('`passkey`') ,'passkey'=>'-----',
		$data=['attivo'=>0,'ruoli_cert',999];
		$up=utenti::where('id', $id)->update($data);
		$resp=array("response"=>"OK");
		return response()->json($resp);
	}	

	public function enable_user(Request $request) {
		$id = $request->input('id_user');
		//'passkey' => DB::raw('`old_pw_for_disable`') ,'old_pw_for_disable'=>null,
		$data=['attivo'=>1];
		$up=utenti::where('id', $id)->update($data);
		$resp=array("response"=>"OK");
		return response()->json($resp);
	}	

	public function update_user(Request $request) {
		$id_user = $request->input('id_user');

		$validator = Validator::make($request->all(), [
			'id_user' => 'required|exists:utenti,id',
			'operatore' => 'required|string|min:5',
			'userid' => [
				'required',
				'string',
				'max:20',
				Rule::unique('utenti')->ignore($id_user),
			],
			'email' => [
				'nullable',
				'email',
				'max:255',
				Rule::unique('utenti')->ignore($id_user),
			],
			'password' => [
				'required',
				'string',
				'max:15',
				//Password::min(8)->mixedCase()->numbers()->symbols()
			],
			'admin_lotti' => 'nullable|integer|in:0,1,2',
			'ruoli_cert' => 'nullable|integer|in:1,2,4,5,6,7,10,999',
			'admin_sos' => 'nullable|integer|in:0,1,2,10',
			'rst_sos' => 'nullable|integer|in:0,1',
			'admin_lp' => 'nullable|integer|in:1,10',
			'admin_mp' => 'nullable|integer|in:0,1,10',
			'vest_access' => 'nullable|integer|in:0,1',
			'nc_access' => 'nullable|integer|in:0,1,2,3,4,5',
		]);
	
		if ($validator->fails()) {
			return response()->json([
				'response' => 'KO', 
				'message' => implode('<br>', $validator->errors()->all())
			], 422);
		}

        $validated = $validator->validated();
		$user = utenti::find($validated['id_user']);
	
		$user->operatore = $validated['operatore'];
		$user->userid = $validated['userid'];
		$user->email = $validated['email'];
	
		// Save the plain-text password for legacy apps
		$user->passkey = $validated['password'];
		// Hash the password for the 'password' column (for Laravel Auth)
		$user->password_hash = Hash::make($validated['password']);
	
		$user->admin_lotti = $validated['admin_lotti'];
		$user->ruoli_cert = $validated['ruoli_cert'];
		$user->admin_sos = $validated['admin_sos'];
		$user->rst_sos = $validated['rst_sos'];
		$user->admin_lp = $validated['admin_lp'];
		$user->admin_mp = $validated['admin_mp'];
		$user->vest_access = $validated['vest_access'];
		$user->nc_access = $validated['nc_access'];
	
		$user->save();
	
		return response()->json(['response' => 'OK', 'user' => $user]);
	}
		

}
