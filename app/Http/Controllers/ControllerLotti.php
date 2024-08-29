<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use App\Models\utenti;
use App\Models\regole;

use DB;
use Mail;


class ControllerLotti extends Controller
{
public function __construct()
	{
		
		if (!Auth::user()) {
				//non riesco ad invocare il logout

		} else  
			$id_user=Auth::user()->id;
		
		$this->middleware('auth')->except(['index']);

	}	


	public function load_modelli(Request $request) {
		$DBmodelli=regole::select('DBmodelli')
			->whereNotNull('DBmodelli')
			->where('DBmodelli','not like','%[%')
			->groupBy('DBmodelli')
			->OrderBy('DBmodelli')
			->get();

		$DBmodelli1=regole::select('DBmodelli1')
			->whereNotNull('DBmodelli1')
			->groupBy('DBmodelli1')
			->OrderBy('DBmodelli1')
			->get();
		
		$modelli=array();
		$modelli[0]['valuex']='-';$modelli[0]['text']='Valore nullo';
		$modelli[1]['valuex']='';$modelli[1]['text']='Definizione manuale';
		$modelli[2]['valuex']="[codice,1].ciff";$modelli[2]['text']="Codice (dal 2° carattere) + .ciff";
		$modelli[3]['valuex']="[codice,0].ciff";$modelli[3]['text']="Codice (dal 1° carattere) + .ciff";

		for ($sca=0;$sca<count($DBmodelli);$sca++)	{
			$value=$DBmodelli[$sca]['DBmodelli'];
			$modelli[$sca+4]['valuex']=$value;
			$modelli[$sca+4]['text']=$value;
		}

		$modelli1=array();
		$modelli1[0]['valuex']='-';$modelli1[0]['text']='Valore nullo';
		$modelli1[1]['valuex']='';$modelli1[1]['text']='Definizione manuale';
		
		for ($sca=0;$sca<count($DBmodelli1);$sca++)	{
			$value=$DBmodelli1[$sca]['DBmodelli1'];
			$modelli1[$sca+2]['valuex']=$value;
			$modelli1[$sca+2]['text']=$value;
		}

		$esito['header']="OK";
		$esito['DBmodelli']=$modelli;
		$esito['DBmodelli1']=$modelli1;

		echo json_encode($esito);
	}
	
	public function rule_lotti(Request $request) {
		$regole=regole::get();
		return view('all_views/rule_lotti',compact('regole'));
	
	}

	public function save_inizia(Request $request) {
		$inizia_con = $request->input('inizia_con');
		$id_edit = $request->input('id_edit');

		$esito=array();

		try{
			if ($id_edit=="new" || strlen($id_edit)==0)
				$regole = new regole;
			else
				$regole = regole::find($id_edit);

			$min_len=$request->input('min_len');
			if ($min_len==null || strlen($min_len)==0) $min_len="";
			$max_len=$request->input('max_len');
			if ($max_len==null || strlen($max_len)==0) $max_len="";
			$len=$request->input('len');
			if ($len==null || strlen($len)==0) $len="";

			$regole->inizia_con =$request->input('inizia_con');
			$regole->min_len =$min_len;
			$regole->max_len =$max_len;
			$regole->len =$len;
			$db_mod=$request->input('DBmodelli');
			if ($db_mod!=null) $db_mod=str_replace("/","-",$db_mod);
			if ($db_mod=="-") $db_mod=null;
			$regole->DBmodelli =$db_mod;
			$db_mod1=$request->input('DBmodelli1');
			if ($db_mod1!=null) $db_mod1=str_replace("/","-",$db_mod1);
			if ($db_mod1=="-") $db_mod1=null;
			$regole->DBmodelli1 =$db_mod1;
			$esito['header']="OK";
			$esito['message']="Sql OK";
			$regole->save();
		}catch (\Illuminate\Database\QueryException $exception){
	 		//dd($exception->getMessage());
			$esito['header']="KO";
			$esito['message']=$exception->getMessage();
		 }

		
		echo json_encode($esito);
	}


	public function save_pattern(Request $request) {
		$pattern = $request->input('pattern');
		$id_edit = $request->input('id_edit');
		
		$esito=array();

		try{
			
			if ($id_edit=="new" || strlen($id_edit)==0)
				$regole = new regole;
			else
				$regole = regole::find($id_edit);			
			$regole->pattern =$request->input('pattern');
			
			$db_mod=$request->input('DBmodelli');
			if ($db_mod!=null) $db_mod=str_replace("/","-",$db_mod);
			if ($db_mod=="-") $db_mod=null;
			$regole->DBmodelli =$db_mod;
			$db_mod1=$request->input('DBmodelli1');
			if ($db_mod1!=null) $db_mod1=str_replace("/","-",$db_mod1);
			if ($db_mod1=="-") $db_mod1=null;
			$regole->DBmodelli1 =$db_mod1;			

			$esito['header']="OK";
			$esito['message']="Sql OK";
			$regole->save();
		}catch (\Illuminate\Database\QueryException $exception){
	 		//dd($exception->getMessage());
			$esito['header']="KO";
			$esito['message']=$exception->getMessage();
		 }

		
		echo json_encode($esito);
	}	
	
	public function testcodice(Request $request) {
		$test_codice = $request->input('test_codice');
		$test_pattern=$this->test_pattern($test_codice);
		$test_inizia=$this->test_inizia($test_codice);
		$test=array();
		$test['test_pattern']=$test_pattern;
		$test['test_inizia']=$test_inizia;
		echo json_encode($test);
	}

	private function test_inizia($codice){
		$resp=array();	
		$el_inizia=$this->load_rule();
		$inizias=$el_inizia['inizia_con'];
		$t=$codice;
		foreach($inizias as $inizia) {

			$min=intval($inizia['min_len']);
			$max=intval($inizia['max_len']);
			$len=intval($inizia['len']);
			$i_ref=$inizia['inizia_con'];

			$arr=explode("|",$i_ref);
			for ($sca=0;$sca<count($arr);$sca++) {
				$i=$arr[$sca];

				$l=strlen($i);
				$check1=false;
				if (!$t || strlen($t)==0) continue;
				if (substr($t,0,$l)==$i) $check1=true;
				if ((strlen($t)==$len && $check1==true) || ($min==null && $max==null && $len==null && $check1==true)) {
					//true
					$resp[$i_ref]=$inizia;
					break;
				}
				if (strlen($t)>=$min && strlen($t)<=$max && $check1==true) {
					//true
					$resp[$i_ref]=$inizia;
					break;
				}
			}				


		}	
		return $resp;	
	
	}

	private function test_pattern($codice) {
		$resp=array();	
		$el_pattern=$this->load_rule();
		$patterns=$el_pattern['pattern'];
		$t=$codice;
		foreach($patterns as $pattern) {
			
			$p_ini=$pattern['pattern'];

			$arr=explode("|",$p_ini);

			for ($iter=0;$iter<count($arr);$iter++) {
				$p=$arr[$iter];
				
				if (strlen($p)!=strlen($t)) continue;
				
				$elem="";$elemP="";$elemT="";
				$fl=0;
				for ($sca=0;$sca<strlen($p);$sca++){
					$elemP=substr($p,$sca,1);
					$elemT=substr($t,$sca,1);
					if ($elemP!="*") {
						if ($elemP=="?") {
							if (!($elemT=="0" || $elemT=="1" || $elemT=="2" || $elemT=="3" || $elemT=="4" || $elemT=="5" || $elemT=="6" || $elemT=="7" || $elemT=="8" || $elemT=="9")){
								$fl=1;
								break;							
							}
						} else {
							if ($elemP!=$elemT) {
								$fl=1;
								break;
							}
						}
					}
				}
				
				if ($fl!=1) {					
					
					$resp[$p_ini]=$pattern;
					break;
				}
			}
				
		}	
		return $resp;
	}

	public function load_rule() {
		$pattern=regole::select('id','pattern','DBmodelli','DBmodelli1','note','updated_at')
		->whereNotNull('pattern')
		->get();
		$inizia_con=regole::select('id','inizia_con','min_len','max_len','len','DBmodelli','DBmodelli1','note','updated_at')
		->whereNotNull('inizia_con')
		->get();
		$arr=array();
		$arr['pattern']=$pattern;
		$arr['inizia_con']=$inizia_con;
		return $arr;
	}

	public function dele_rule(Request $request) {
		$id_dele = $request->input('id_dele');
		$dele = regole::find($id_dele)->delete();
		$esito=array();
		$esito['header']="OK";
		echo json_encode($esito);
	}

}
