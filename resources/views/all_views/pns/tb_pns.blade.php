@foreach($elenco_pns as $pns)
	<?php
		$colo_status="danger";$status_text="NoRev";

		$sign_ready=0;
		if ($pns->sign_ft!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_etichetta!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_scheda_t!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_scheda_s!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_cert!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_udi!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_altro!=null) {$colo_status="warning";$sign_ready++;}
		if ($pns->sign_tecnica!=null) {$colo_status="warning";$sign_ready++;}

		$check_ready_sign=7;
		if ($pns->ivd=="IVD" || $pns->ivd=="RIVIVD") $check_ready_sign=8;
		
		//check documentazione tecnica pronta per la firma
		$doc_tecnica="0";$file_tec=1;
		
		//$pns->tecnica_eudamed_note &&
		if ($pns->tecnica_file_note!=null &&
			$pns->tecnica_file_data &&
			$pns->tecnica_repertorio &&
			$pns->tecnica_ministero_data &&
			$pns->tecnica_basic_udi && 
			($pns->tecnica_eudamed_sn==0 || ($pns->tecnica_eudamed_sn==1  && $pns->tecnica_eudamed_data))
			)	{
			$doc_tecnica="1";
		}
		$url_file="";
		if (!file_exists("allegati/".$pns->id."/doc_tecnici/".$pns->id.".pdf")) {
			$file_tec=0;
			$doc_tecnica=0;
		} else 
			$url_file="allegati/".$pns->id."/doc_tecnici/".$pns->id.".pdf";
		
		$sign_tecnica=$pns->sign_tecnica;


		if ($colo_status=="warning") $status_text="InRev";
		if ($sign_ready==$check_ready_sign || ($pns->ivd=="IVD" && $pns->progetto_rd_sn=="S" && $pns->sign_recensione==1)) {
			$status_text="ReadySign";
			$colo_status="warning";
		}
		
		if ($pns->sign_qa!=null) {
			$colo_status="success";$status_text="Close";
		}		
		
		
		
	?>
	<tr>

		<td style='width:40px'>
		 @if ($pns->dele=="1") 
			<font color='red'><del> 
		 @endif
			<span id='id_descr{{$pns->id}}' data-codice='{{ $pns->codice }}'>
				{{ $pns->codice }}
			</span>	
		 @if ($pns->dele=="1") 
			 </del></font>
				<hr>
				<small><i>{{ $pns->motivazione_dele }}</i></small>								 
		 @endif	
		</td>	
		<td style='width:70px;text-align:center'>
			@if ($pns->dele=="0")
				@if ($pns->sign_recensione==null)
				<a href="{{route('recensione',['id'=>$pns->id])}}" >
					<button type="button" class="btn btn-primary" alt='Completa scheda'><i class="fas fa-edit fa-xs" title="Completa scheda"></i></button>
				</a>									
				@elseif($pns->sign_qa==null && $sign_ready!=$check_ready_sign && $pns->progetto_rd_sn!="S")
				<a href="{{route('recensione',['id'=>$pns->id])}}" >
					<button type="button" class="btn btn-info" alt='Visualizza scheda'><i class="fas fa-info-circle fa-xs" title="Visualizza scheda"></i></button>
				</a>									
				@elseif(($sign_ready==$check_ready_sign || (($pns->ivd=="IVD" && $pns->progetto_rd_sn=="S" && $pns->sign_recensione==1)) )&& $pns->sign_qa==null)
					<a href="{{route('recensione',['id'=>$pns->id])}}" >
						<button type="button" class="btn btn-primary" alt='SignQA'><i class="fas fa-signature fa-xs" title="SignQA"></i></button>
					</a>
				@else
					<a href="{{route('recensione',['id'=>$pns->id])}}" >
						<button type="button" class="btn btn-info" alt='Visualizza scheda'><i class="fas fa-info-circle fa-xs" title="Visualizza scheda"></i></button>
					</a>					
				@endif
			@endif

		</td>


		<td style='text-align:center;width:40px'>
			<button style='width:80px' type="button" class="btn btn-{{$colo_status}} btn-sm">{{$status_text}}</button>
			<span class='firme' style='display:none'>
				<?php
					if ($pns->sign_qa!=null && isset($arr_utenti[$pns->sign_qa]['ref'])) {
						$op=$arr_utenti[$pns->sign_qa]['operatore'];
						echo "<small>";
							echo "<a href='javascript:void(0)' 
									onclick=\"alert('$op')\">";
									echo $arr_utenti[$pns->sign_qa]['ref'];
							echo "</a>";		
						echo "</small>";
					}
				?>
			</span>
		</td>
		


		<td>
			<i>{{ $pns->descrizione }}</i>
		</td>

		<td style='width:120px'>
			{{ $pns->created_at }}
		</td>
		<td style='width:120px'>
			{{ $pns->updated_at }}
		</td>


		<td style='width:70px'>
			{{ $pns->ivd }}

		</td>


		
		<td style='min-width:400px'>
		<?php
			$stato_sign="disabled";
			if ($pns->sign_recensione!=null) $stato_sign="";

			$colo_stato_etic="danger";
			$colo_stato_ft="danger";
			$colo_stato_tec="danger";
			$colo_stato_sic="danger";
			$colo_stato_cert="danger";
			$colo_stato_udi="danger";
			$colo_stato_altro="danger";
			$colo_stato_tecnica="danger";

			$etic_status=0;
			if ($pns->sign_etichetta!=null) {
				$etic_status=1;
				$colo_stato_etic="success";
			}	
			$ft_status=0;
			if ($pns->sign_ft!=null) {
				$ft_status=1;
				$colo_stato_ft="success";
			}
			
			$scheda_t_status=0;
			if ($pns->sign_scheda_t!=null) {
				$scheda_t_status=1;
				$colo_stato_tec="success";
			}	
			$scheda_s_status=0;
			if ($pns->sign_scheda_s!=null) {
				$scheda_s_status=1;
				$colo_stato_sic="success";
			}	
			$cert_status=0;
			if ($pns->sign_cert!=null) {
				$cert_status=1;
				$colo_stato_cert="success";
			}				
			$udi_status=0;
			if ($pns->sign_udi!=null) {
				$udi_status=1;
				$colo_stato_udi="success";
			}
			$altro_status=0;
			if ($pns->sign_altro!=null) {
				$altro_status=1;
				$colo_stato_altro="success";
			}

			$tecnica_status=0;
			if ($pns->sign_tecnica!=null) {
				$tecnica_status=1;
				$colo_stato_tecnica="success";
			}


			
			if ($pns->ivd=="IVD" && $pns->progetto_rd_sn=="S" && $pns->sign_recensione==1) {
				$colo_stato_etic="success";
				$colo_stato_ft="success";
				$colo_stato_tec="success";
				$colo_stato_sic="success";
				$colo_stato_cert="success";
				$colo_stato_udi="success";
				$colo_stato_altro="success";
				$colo_stato_tecnica="success";
				$stato_sign="disabled";
			}				
			
			$view_doc="display:none";
		?>	
			@if ($pns->dele=="0") 
				<?php
					if ($ft_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=8;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->file_ft."';";
					if ($ft_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>
				<a href="javascript:void(0)" title="Fattibilità tecnica">
					<button type="button" class="btn btn-{{$colo_stato_ft}}" onclick="{{$js}}" {{$stato_sign}}><i class="fas fa-cogs fa-xs"></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_etichetta");
					echo $view_sign;
				?>
				</span>					
			
			
				<?php
					if ($etic_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=1;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->file_etic."';";
					if ($etic_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>
				<a href="javascript:void(0)" title="Etichetta">
					<button type="button" class="btn btn-{{$colo_stato_etic}}" onclick="{{$js}}" {{$stato_sign}}><i class="fas fa-tag fa-xs" ></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_etichetta");
					echo $view_sign;
				?>
				</span>	




				<?php
					if ($scheda_t_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=2;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->url_scheda_t."';";
					if ($scheda_t_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>
				<a href="javasript:void(0)"  title="Scheda tecnica">
					<button type="button" class="btn btn-{{$colo_stato_tec}}" onclick="{{$js}}" {{$stato_sign}}><i class="fas fa-file-invoice fa-xs"></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_scheda_t");
					echo $view_sign;
				?>			
				</span>
				
				<?php
					if ($scheda_s_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=3;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->url_scheda_s."';";
					if ($scheda_s_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>				
				<a href="javasript:void(0)" title="Scheda sicurezza">
					<button type="button" class="btn btn-{{$colo_stato_sic}}" onclick="{{$js}}" {{$stato_sign}}>
					<i class="fas fa-shield-alt fa-xs" ></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_scheda_s");
					echo $view_sign;
				?>			
				</span>
				<?php
				
				
					if ($cert_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=4;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->url_cert."';";
					if ($cert_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>		
				<a href="javascript:void(0)"  title="Certificato">
					<button type="button" class="btn btn-{{$colo_stato_cert}}"  onclick="{{$js}}" {{$stato_sign}}>
					<i class="fas fa-check-square fa-xs"></i></button>
				</a>										
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_cert");
					echo $view_sign;
				?>				
				</span>				
				
				
				<?php
					if ($udi_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=5;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->udi_di."';";
					if ($udi_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>					
				<a href="javascript:void(0)" title="UDI-DI">
					<button type="button" class="btn btn-{{$colo_stato_udi}}" alt='UDI-DI'   onclick="{{$js}}" {{$stato_sign}}  ><i class="fas fa-file fa-xs"></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_udi");
					echo $view_sign;
				?>				
				</span>

				<?php
					if ($altro_status==0) $proc="ins_doc";
					else $proc="view_doc";
					$js="";					
					$js.="$proc.from=6;";
					$js.="$proc.id_pns=".$pns->id.";";
					$js.="$proc.sign_qa='".$pns->sign_qa."';";
					$js.="$proc.resource_file='".$pns->altri_doc."';";
					if ($altro_status==0) $js.="ins_doc();";
					else $js.="view_doc();";
				?>					
				<a href="javascript:void(0)" title="Controllo listini e altri documenti">
					<button type="button" class="btn btn-{{$colo_stato_altro}}" alt='Controllo listini e altri documenti'   onclick="{{$js}}" {{$stato_sign}}  ><i class="fas fa-file-alt fa-xs"></i></button>
				</a>
				<span class='firme' style='display:none'>
				<?php
					$view_sign=view_sign($arr_utenti,$pns,"sign_altro");
					echo $view_sign;
				?>				
				</span>
				
				@if ($pns->ivd=="IVD" || $pns->ivd=="RIVIVD")
					<span id='info_tecnica{{$pns->id}}'
					data-tecnica_file_note='{{ $pns->tecnica_file_note }}'
					data-tecnica_file_data='{{ $pns->tecnica_file_data }}'
					data-tecnica_repertorio='{{ $pns->tecnica_repertorio }}'
					data-tecnica_ministero_data='{{ $pns->tecnica_ministero_data }}'
					data-tecnica_basic_udi='{{ $pns->tecnica_basic_udi }}'
					data-tecnica_eudamed_note='{{ $pns->tecnica_eudamed_note }}'
					data-tecnica_eudamed_sn='{{ $pns->tecnica_eudamed_sn }}'
					data-tecnica_eudamed_data='{{ $pns->tecnica_eudamed_data }}' 
					data-doc_tecnica='{{ $doc_tecnica }}'
					data-file_tec='{{ $file_tec }}'
					data-url_file='{{ $url_file }}'
					data-sign_tecnica='{{ $sign_tecnica }}'>
					</span>

					
						<?php

				
						$proc="ins_doc";
						//else $proc="view_doc";
						$js="";					
						$js.="$proc.from=7;";
						$js.="$proc.id_pns=".$pns->id.";";
						$js.="$proc.sign_qa='".$pns->sign_qa."';";
						$js.="$proc.resource_file='".$pns->altri_doc."';";
						$js.="ins_doc();";
						//else $js.="view_doc();";
					?>					
					<a href="javascript:void(0)" title="Documentazione tecnica">
						<button type="button" class="btn btn-{{$colo_stato_tecnica}}" alt='Documentazione tecnica' onclick="{{$js}}" {{$stato_sign}}  ><i class="fas fa-wrench fa-xs"></i></button>
					</a>
					<span class='firme' style='display:none'>
					<?php
						$view_sign=view_sign($arr_utenti,$pns,"sign_tecnica");
						echo $view_sign;
					?>				
					</span>
				@endif
				
				
				
				<a href='#' onclick="log_event({{$pns->id}})">
					<button type="submit" class="btn btn-secondary" title="Log eventi">
					<i class="fas fa-search fa-xs"></i></button>	
				</a>				

				<a href='#' onclick="dele_element({{$pns->id}})">
					<button type="button" name='dele_ele' class="btn btn-secondary" title="Elimina PNS">
					<i class="fas fa-trash fa-xs"></i></button>	
				</a>
			@endif
			@if ($pns->dele=="1") 
				<a href='#'onclick="restore_element({{$pns->id}})" >
					<button type="button" class="btn btn-secondary" alt='Restore' title="Elimina PNS"><i class="fas fa-trash-restore"></i></button>
				</a>
			@endif
			
			
		</td>	
	</tr>
@endforeach

<?php
	function view_sign($arr_utenti,$pns,$sign) {
		$view=null;
		if ($pns->$sign!=null && isset($arr_utenti[$pns->$sign]['ref'])) {
			$view.= "<small>";
				$op=$arr_utenti[$pns->$sign]['operatore'];
				$view.= "<a href='javascript:void(0)' 
						onclick=\"alert('$op')\">";
						$view.= $arr_utenti[$pns->$sign]['ref'];
				$view.= "</a>";		
			$view.= "</small>";
		}
		return $view;
	}
?>	