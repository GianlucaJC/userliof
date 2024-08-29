<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use App\Models\utenti;

use DB;
use Mail;


class mainController extends Controller
{
public function __construct()
	{
		
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
		/*
		$last_ts_target=last_ts_target::where('id','=',1)->get();
		//in caso di prima importazione decidere data fittizia di inizio import
		$data_importdb="";
		if (isset($last_ts_target[0])) {
			$data_importdb=$last_ts_target[0]->last_ts;
		}	
		$datax=date("Y-m-d H:i:s");
		$data1 = strtotime("-1 days", strtotime($datax));

		$d2=date("Y-m-d H:i:s", $data1);
		if (strlen($data_importdb)!=0 && $data_importdb<$d2) {
			
			$data_import=$data_importdb;
		}
		else {
			
			$data_import=$d2;
		}	
		
		if (strlen($data_import)>0) $this->import_code($data_import);

		if ($request->has("btn_save")) {
			$email_notif=$request->input('email_notif');
			$email_notif_green=$request->input('email_notif_green');
			$codici_esclusi=$request->input('codici_esclusi');
			$db_set = db_set::find(1);
			if	(!isset($db_set)) $db_set=new db_set;
			$db_set->email_notif=$email_notif;
			$db_set->email_notif_green=$email_notif_green;
			$db_set->codici_esclusi=$codici_esclusi;
			$db_set->save();
		}
		$db_set = db_set::find(1);
		$email_notif="";$email_notif_green="";$codici_esclusi="";
		if	(isset($db_set)) {
			$email_notif=$db_set->email_notif;
			$email_notif_green=$db_set->email_notif_green;
			$codici_esclusi=$db_set->codici_esclusi;
		}
		*/

		$email_notif="";
		$email_notif_green="";
		$codici_esclusi="";

		$view_dele=$request->input("view_dele");
		if (strlen($view_dele)==0) $view_dele=0;
		if ($view_dele=="on") $view_dele=1;
		
		
		$utenti=DB::table('utenti')
		->when($view_dele=="1", function ($utenti) {
			return $utenti->whereNotNull('old_pw_for_disable');
		})
		->get();		
		

		return view('all_views/dashboard',compact('email_notif','email_notif_green','codici_esclusi','utenti','view_dele'));
	
	}
	
	public function load_info(Request $request) {
		$id_user = $request->input('id_user');
		
		$utenti=utenti::where('id', $id_user)->get();
		echo json_encode($utenti);
	}	
	
	public function disable_user(Request $request) {
		$id = $request->input('id_user');
		$data=['old_pw_for_disable' => DB::raw('`passkey`') ,'passkey'=>'-----'];
		$up=utenti::where('id', $id)->update($data);
		$resp=array("response"=>"OK");
		echo json_encode($resp);
	}	

	public function enable_user(Request $request) {
		$id = $request->input('id_user');
		$data=['passkey' => DB::raw('`old_pw_for_disable`') ,'old_pw_for_disable'=>null];
		$up=utenti::where('id', $id)->update($data);
		$resp=array("response"=>"OK");
		echo json_encode($resp);
	}	
		

}
