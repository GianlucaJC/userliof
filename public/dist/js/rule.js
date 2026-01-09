$(document).ready( function () {
	init_datatables();
	imposta_app_regole();
} );

function init_datatables() {
	var table=$('#tb_regole').DataTable({
		pagingType: 'full_numbers',
		dom: 'lfrtBip',
		buttons: [
			'excel','pdf'
		],		
		searching: true,
		paging:true,
		filter: true,
		info: true,
		pageLength: 10,
		lengthMenu: [8, 10, 15, 20, 50, 100, 200, 500],
		language: {
			lengthMenu: 'Visualizza _MENU_ regole per pagina',
			zeroRecords: 'Nessuna regola trovata',
			info: 'Pagina _PAGE_ di _PAGES_',
			infoEmpty: 'Non sono disponibili regole',
			infoFiltered: '(Filtrati da _MAX_ regole totali)',
		},
	});
}

function imposta_app_regole() {
	Vue.component('regole-a', {
		template:
		`
		<div v-if='id_edit!=0' >

			<div v-if="edit_pattern==true">
				<button type="button" class="btn btn-primary  btn-lg btn-block" v-on:click="btn_pattern=true;btn_inizia_con=false" >Pattern</button>
			
				<div class='container-fluid mt-3' v-if="btn_pattern==true">
					<div class="row justify-content-center">
						<div class="col-md-12">
							<div v-if="savepattern==true">
								<div class="alert alert-success mt-2" role="alert" v-if="savepattern_esito==true">
										<b>Dati salvati con successo!</b>
										<hr>
										
								</div>

								<div class="alert alert-warning mt-2" role="alert" v-if="savepattern_esito==false">
										<b>Attenzione!</b>
										<hr>
										Si è verificato un problema durante il salvataggio del pattern!
										Potrebbe essere dovuto dal fatto che si tratta di un pattern già esistente
									</div>
							</div>

							<div class="card" v-if="savepattern==false || savepattern_esito==false">
								<div class="card-header">
									<h4>Definizione regola tramite pattern (es: $12*?L)</h4>
									<h6><i><b>*</b> segnaposto per qualsiasi carattere alfanumerico<br><b>?</b> segnaposto per qualsiasi carattere numerico</i></h6>
								</div>


								<div class="card-body">
									<form @submit.prevent="validate">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="pattern" placeholder="Regola pattern" v-model="pattern" maxlength="255" required>
													<label for="pattern">Pattern (usa | per più pattern)</label>
												</div>
											</div>
										</div>	
										<div class="row">
											<div class="col-md-3">
												<div class="form-floating mb-3 mb-md-0">
													<select class="form-select" id="DBmod" aria-label="Definizione DBmodelli" name='DBmod' v-model="DBmod" >
														<option v-for="modello in modelli" :value="modello.valuex" :selected="modello.valuex==DBmod">
															{{ modello.text }}
														</option>
													</select>
													<label for="DBmod">Definizione DBmodelli</label>												
												</div>
											</div>									

											<div class="col-md-3" v-show="DBmod==''">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="DBmodelli" placeholder="DBmodelli" v-model="DBmodelli">
													<label for="DBmodelli">DBmodelli manuale</label>
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="form-floating mb-3 mb-md-0">
													
													<select class="form-select" id="DBmod1" aria-label="Definizione DBmodelli1" name='DBmod1' v-model="DBmod1" >
														<option v-for="modello in modelli1" :value="modello.valuex" :selected="modello.valuex==DBmod1">
															{{ modello.text }}
														</option>
													</select>
													<label for="DBmod1">Definizione DBmodelli1</label>													
												</div>
											</div>	
											
											<div class="col-md-3" v-show="DBmod1==''">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="DBmodelli1" placeholder="DBmodelli1" v-model="DBmodelli1">
													<label for="DBmodelli1">DBmodelli1</label>
												</div>
											</div>                                    
									</div> 

									<hr>
										<button class="btn btn-primary" :disabled='btn_edit_active' @click="save_pattern()">Salva regola</button>
										<button class="btn btn-secondary ms-2" type="button" @click="btn_pattern=false;btn_inizia_con=false;savepattern=false">Chiudi sezione Pattern</button>
										<hr>
										
										<div class="row">
											<div class="col-md-4">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="test_pattern" placeholder="Regola pattern" v-model="test_pattern">
													<label for="test_pattern">Test Pattern</label>
												</div>
											</div>
											<div class="col-xs-4">
												<button class="btn btn-success" :disabled='btn_esegui' @click="run_test()" type="button">
													Esegui
												</button>
												<div v-if="esito_test">
													
													<div class="alert alert-success mt-2" v-if="result_test==true" role="alert">
														<b>Ottimo!</b> Il dato inserito è coerente con il pattern!
													</div>		

													<div class="alert alert-warning mt-2" v-if="result_test==false" role="alert">
														<b>Attenzione!</b> Il dato inserito NON è coerente con il pattern!
													</div>		
																				
												</div>
											</div>
										</div> 
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
		  </div>	


		  <div class='mt-2' v-if="edit_inizia==true">
				<button type="button" class="btn btn-primary  btn-lg btn-block" v-on:click="btn_pattern=false;btn_inizia_con=true" >Inizia con o uguale a</button>
				

				<div class='container-fluid mt-3' v-if="btn_inizia_con==true">
					<div class="row justify-content-center">
						<div class="col-md-12">
							<div v-if="saveinizia==true">
								<div class="alert alert-success mt-2" role="alert" v-if="saveinizia_esito==true">
										<b>Dati salvati con successo!</b>
										<hr>
								</div>

								<div class="alert alert-warning mt-2" role="alert" v-if="saveinizia_esito==false">
										<b>Attenzione!</b>
										<hr>
										Si è verificato un problema durante il salvattaggio della stringa inizia con!
										Potrebbe essere dovuto dal fatto che si tratta di una regola già esistente
									</div>
							</div>

							<div class="card">
								<div class="card-header">
									<h4>Definizione regola tramite stringa che inizia con o esattamente uguale a</h4>
								</div>
								<div class="card-body">
									<form @submit.prevent="validate">
										<div class="alert alert-danger" role="alert" v-if="err_inizia">
											{{ errore_inizia }}
										</div>									
										<div class="row">
											<div class="col-md-3">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="inizia_con" placeholder="Stringa di riferimento" v-model="inizia_con" @input="update_inizia()"  maxlength="255"  required>
													<label for="inizia_con">Inizia con (usa | per più stringhe)</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="min_len" placeholder="Lunghezza minima" v-model="min_len" @input="update_inizia()">
													<label for="min_len">Minimo caratteri</label>
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="max_len" placeholder="Lunghezza massima" v-model="max_len" @input="update_inizia()">
													<label for="max_len">Massimo caratteri</label>
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="len" placeholder="Lunghezza" v-model="len" @input="update_inizia()">
													<label for="len">Lunghezza esatta</label>
												</div>
											</div>
											

										</div>	
										<div class="row">	
											<div class="col-md-3">
												<div class="form-floating mb-3 mb-md-0">
													<select class="form-select" id="iniDBmod" aria-label="Definizione DBmodelli" name='iniDBmod' v-model="iniDBmod" >
														<option v-for="modello in modelli" :value="modello.valuex" :selected="modello.valuex==iniDBmod">
															{{ modello.text }}
														</option>
													</select>
													<label for="iniDBmod">Definizione DBmodelli</label>
												</div>
											</div>										
											<div class="col-md-3" v-show="iniDBmod==''">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="iniDBmodelli" placeholder="DBmodelli" v-model="iniDBmodelli">
													<label for="iniDBmodelli">DBmodelli manuale</label>
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-floating mb-3 mb-md-0">
													<select class="form-select" id="iniDBmod1" aria-label="Definizione DBmodelli1" name='iniDBmod1' v-model="iniDBmod1">
														<option v-for="modello in modelli1" :value="modello.valuex" :selected="modello.valuex==iniDBmod1">
															{{ modello.text }}
														</option>
													</select>
													<label for="iniDBmod1">Definizione DBmodelli1</label>
												</div>
											</div>										
											<div class="col-md-3" v-show="iniDBmod1==''">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="iniDBmodelli1" placeholder="DBmodelli1" v-model="iniDBmodelli1">
													<label for="iniDBmodelli1">DBmodelli1 manuale</label>
												</div>
											</div>                                    
									</div> 
										<hr>
										<button class="btn btn-primary" :disabled='btn_save_inizia' @click="save_inizia()">Salva regola</button>
										<button class="btn btn-secondary ms-2" type="button" @click="btn_pattern=false;btn_inizia_con=false;saveinizia=false">Chiudi sezione Inizia con</button>
										<hr>
										
										<div class="row">
											<div class="col-md-4">
												<div class="form-group form-floating mb-3">
													<input type="text" class="form-control" id="test_inizia" placeholder="Test inizia" v-model="test_inizia" @input="update_inizia()">
													<label for="test_inizia">Test Regola</label>
												</div>
											</div>
											<div class="col-xs-4">
												<button class="btn btn-success" :disabled='btn_esegui_inizia' @click="run_inizia()" type="button">
													Esegui
												</button>
												<div v-if="esito_inizia">
													
													<div class="alert alert-success mt-2" v-if="result_inizia==true" role="alert">
														<b>Ottimo!</b> Il dato inserito è coerente con la regola!
													</div>		

													<div class="alert alert-warning mt-2" v-if="result_inizia==false" role="alert">
														<b>Attenzione!</b> Il dato inserito NON è coerente con la regola!
													</div>		
																				
												</div>
											</div>
										</div> 
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			
			
			<button type="button" class="mt-4 btn btn-secondary  btn-lg btn-block" onclick="if (Regole.saveinizia==true || Regole.savepattern==true){$('.refr').hide();$('#div_refr'+Regole.id_edit).show()} $('#div_elenco').show(120)" v-on:click="id_edit=0" >Torna elenco regole</button>
		</div>
		`,
		props: ['foodName','foodDesc','isFavorite'],
		data() {
		   let edit_pattern=false;
		   let edit_inizia=false;	
		   let DBmod="-";
		   let DBmod1="-";
		   let savepattern=false;
		   let saveinizia=false;
		   let savepattern_esito=false;
		   let saveinizia_esito=false;
		   let result_test=false;
		   let test_pattern=""
		   let esito_test=false
		   let pattern;
		   let DBmodelli="";
		   let DBmodelli1="";
		   let btn_edit_active=true;

		   let iniDBmodelli="";let iniDBmodelli1="";
		   let iniDBmod="";let iniDBmod1="";
		   
		   let btn_save_inizia=false;
		   let btn_esegui=true; 
		   let id_edit=0;
		   let btn_pattern=false;

		   let esito_inizia=false
		   let btn_inizia_con=false
		   let inizia_con=""
		   let min_len="";let max_len="";let len="";
		   let result_inizia=false;
		   let btn_esegui_inizia=true; 
		   let modelli=""
		   let modelli1=""
		   let err_inizia=false,errore_inizia=""
		  return {
			id_edit,
			edit_pattern,
			edit_inizia,
			DBmod,
			DBmod1,
			savepattern,
			saveinizia,
			savepattern_esito,
			saveinizia_esito,
			result_test,
			test_pattern,
			esito_test,
			pattern,
			DBmodelli,
			DBmodelli1,
			btn_edit_active,
			btn_save_inizia,
			btn_esegui,
			btn_pattern,
			iniDBmodelli,iniDBmodelli1,
			iniDBmod,iniDBmod1,		
			btn_inizia_con,
			esito_inizia,
			inizia_con,
			btn_esegui_inizia,
			result_inizia,
			min_len,max_len,len,
			modelli,modelli1,
			err_inizia,errore_inizia
		  }
		},
		mounted: function () {
				window.Regole=this;
				this.load_modelli()
			},	
		watch:{
			DBmod(newval,oldval) {
				if (newval=="-") this.DBmodelli=""
				else this.DBmodelli=newval
			},
			DBmod1(newval,oldval) {
				if (newval=="-") this.DBmodelli1=""
				else this.DBmodelli1=newval
			},
			iniDBmod(newval,oldval) {
				this.update_inizia()
				if (newval=="-") this.iniDBmodelli=""
				else this.iniDBmodelli=newval
			},	
			iniDBmod1(newval,oldval) {
				this.update_inizia()
				if (newval=="-") this.iniDBmodelli1=""
				else this.iniDBmodelli1=newval
			},						
			test_pattern(newval,oldval) {
				this.esito_test=false
				this.btn_esegui=false
				if (newval.length==0 || this.pattern.length==0) this.btn_esegui=true
			},	
			pattern(newval,oldval) {
				console.log("newval",newval)
				if (this.savepattern==false || (this.savepattern==true && this.savepattern_esito==false)) {
					this.esito_test=false
					this.btn_esegui=true
					if (newval.length>0 && this.test_pattern.length>0) 
						this.btn_esegui=false
					this.btn_edit_active=false;
					if (newval.length<3) this.btn_edit_active=true
				}
			}	
		},		
		methods: { 
		  load_modelli() {
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("load_modelli", {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
						"X-CSRF-Token": csrf
					},
					body: "load_modelli=1"
				})
				.then(response => {
					if (response.ok) {
						return response.json();
					}
				})
				.then(resp=>{
					console.log(resp)
					console.log("resp.header",resp.header)
					
					if (resp.header=='OK') {
						this.modelli=resp.DBmodelli
						this.modelli1=resp.DBmodelli1
						//athis.emptyinfo();	
					} else {
						this.saveinizia=true
						this.saveinizia_esito=false
					}
						


				})
				.catch(err => {
					console.error("Errore durante il caricamento dei modelli:", err);
				})
					
		  },

		  reset_all() {
		   this.DBmod="-";
		   this.DBmod1="-";
		   this.savepattern=false;
		   this.saveinizia=false;
		   this.savepattern_esito=false;
		   this.saveinizia_esito=false;
		   this.result_test=false;
		   this.test_pattern=""
		   this.esito_test=false
		   this.pattern="";
		   this.DBmodelli="";
		   this.DBmodelli1="";
		   this.btn_edit_active=true;

		   this.iniDBmodelli="";this.iniDBmodelli1="";
		   this.iniDBmod="";this.iniDBmod1="";
		   this.btn_save_inizia=false;
		   this.btn_esegui=true; 
		   this.id_edit=0;
		   this.btn_pattern=false;

		   this.esito_inizia=false
		   this.btn_inizia_con=false
		   this.inizia_con=""
		   this.min_len="";this.max_len="";this.len="";
		   this.result_inizia=false;
		   this.btn_esegui_inizia=true;
		  },

		  update_inizia() {
			this.saveinizia=false
			this.esito_inizia=false;
			this.btn_save_inizia=true
			this.btn_esegui_inizia=true
			this.btn_edit=true
			this.err_inizia=false
			this.errore_inizia=""
			if (this.inizia_con.length>0) {
				console.log("min_len",this.min_len,"max_len",this.max_len,"len",this.len)
				if ((this.min_len>0 && this.max_len>0) || this.len>0) {
					this.btn_save_inizia=false
					this.btn_esegui_inizia=false
				}
				if (!this.min_len && !this.max_len>0 && !this.len>0) {
					this.btn_save_inizia=false
					this.btn_esegui_inizia=false
				}
				if ((this.min_len>0 && this.len>0) || (this.max_len>0 && this.len>0)) {
					this.btn_save_inizia=true
					this.btn_esegui_inizia=true
					this.err_inizia=true
					this.errore_inizia="Definire minimo caratteri e massimo caratteri insieme oppure solo Lunghezza esatta o nessuna lunghezza!"
				}
				if (this.min_len>0 && this.max_len>0 && parseInt(this.min_len)>parseInt(this.max_len)) {
					this.btn_save_inizia=true
					this.btn_esegui_inizia=true
					this.err_inizia=true
					this.errore_inizia="Minimo caratteri maggiore di massimo caratteri!"				
				}
			}

		  },	
		  run_inizia() {
			this.esito_inizia=false;
			setTimeout(() => {
				this.esito_inizia=true
				this.result_inizia=false
				let min=parseInt(this.min_len)
				let max=parseInt(this.max_len)
				let len=parseInt(this.len)
				if (isNaN(min)) min=0
				if (isNaN(max)) max=0
				if (isNaN(len)) len=0

				let i_ref=this.inizia_con
				let arr=i_ref.split("|")
				let i;
				let t=this.test_inizia
				for (let sca=0;sca<arr.length;sca++) {
					i=arr[sca]
					let l=i.length;
					let check1=false
					if (!t || t.length==0) continue
					if (t.substr(0,l)==i) check1=true
					if ((t.length==len && check1==true) || (min==0 && max==0 && len==0 && check1==true)) {
						this.result_inizia=true
						break
					}
					if (t.length>=min && t.length<=max && check1==true) {
						this.result_inizia=true
						break
					}
					
				}
				
			}, 300);
			
		  
		  },

		  
		  save_inizia() {
			Swal.fire({
				title: "Salvataggio Regola",
				text: "Sicuri di salvare la regola?",
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sì, salva!',
				cancelButtonText: 'Annulla'
			}).then((result) => {
				if (result.isConfirmed) {
					this.btn_save_inizia=true
					
					const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
					const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
					fetch("save_inizia", {
						method: 'post',
						headers: {
							"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
							"X-CSRF-Token": csrf
						},
						body: "id_edit="+this.id_edit+"&inizia_con="+this.inizia_con+"&min_len="+this.min_len+"&max_len="+this.max_len+"&len="+this.len+
						"&DBmodelli="+this.iniDBmodelli+"&DBmodelli1="+this.iniDBmodelli1
					})
					.then(response => {
						if (response.ok) {
							return response.json();
						}
					})
					.then(resp=>{
						if (resp.header=='OK') {
							this.saveinizia=true
							this.saveinizia_esito=true
						} else {
							this.saveinizia=true
							this.saveinizia_esito=false
						}
					})
					.catch(err => {
						Swal.fire('Errore!', 'Si è verificato un errore di rete.', 'error');
						console.log(err);
					});
				}
			});
		  },

		  emptyinfo() {
			this.pattern=""
			this.DBmodelli=""
			this.DBmodelli1=""
			this.test_pattern=""			
			this.inizia_con=""
			this.min_len=""
			this.max_len=""
			this.len=""
			this.iniDBmodelli=""
			this.iniDBmodelli1=""
			this.test_inizia=""
			},

		  save_pattern() {
			Swal.fire({
				title: "Salvataggio Regola",
				text: "Sicuri di salvare la regola?",
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sì, salva!',
				cancelButtonText: 'Annulla'
			}).then((result) => {
				if (result.isConfirmed) {
					this.btn_edit_active=true
					
					const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
					const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
					fetch("save_pattern", {
						method: 'post',
						headers: {
							"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
							"X-CSRF-Token": csrf
						},
						body: "id_edit="+this.id_edit+"&pattern="+this.pattern+"&DBmodelli="+this.DBmodelli+"&DBmodelli1="+this.DBmodelli1
					})
					.then(response => {
						if (response.ok) {
							return response.json();
						}
					})
					.then(resp=>{
						if (resp.header=='OK') {
							this.savepattern=true
							this.savepattern_esito=true
							this.emptyinfo();	
						} else {
							this.savepattern=true
							this.savepattern_esito=false
						}
					})
					.catch(err => {
						Swal.fire('Errore!', 'Si è verificato un errore di rete.', 'error');
						console.log(err);
					});
				}
			});
		  },


		  run_test() {
			this.esito_test = false;
			setTimeout(() => {
				this.esito_test = true;
				this.result_test=false
				let p_ini=this.pattern
				let arr=p_ini.split("|")
				
				let t=this.test_pattern
				let p;
				for (let iter=0;iter<arr.length;iter++) {
					p=arr[iter]
					if (p.length!=t.length) continue;
					let elem="";let elemP="";let elemT=""
					let fl=0
					for (let sca=0;sca<p.length;sca++){
						elemP=p.substr(sca,1)
						elemT=t.substr(sca,1)
						if (elemP!="*") {
							if (elemP=="?") {
								if (!(elemT==0 || elemT==1 || elemT==2 || elemT==3 || elemT==4 || elemT==5 || elemT==6 || elemT==7 || elemT==8 || elemT==9))	{
									fl=1
									break							
								}
							} else {
								if (elemP!=elemT) {
									fl=1
									break
								}
							}
						}
					}
					if (fl!=1) {
						this.result_test=true
						break
					}
				}
				
			}, 300);
		  }
		}
	});

	new Vue({
		el: '#app'
	});
}

function new_rule() {
	$("#div_elenco").hide()
	window.Regole.reset_all()
	window.Regole.id_edit = "new"
	window.Regole.edit_pattern = true
	window.Regole.edit_inizia = true

}

function dele_rule(id_ref) {
	Swal.fire({
		title: "Eliminazione Regola",
		text: "Sicuri di cancellare la regola? L'operazione non è reversibile.",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Sì, cancella!',
		cancelButtonText: 'Annulla'
	}).then((result) => {
		if (result.isConfirmed) {
			const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
			const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
			fetch("dele_rule", {
				method: 'post',
				headers: {
					"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
					"X-CSRF-Token": csrf
				},
				body: "id_dele="+id_ref
			})
			.then(response => {
				if (response.ok) {
					return response.json();
				}
			})
			.then(resp=>{
				if (resp.header=='OK') {
					$('#tb_regole').DataTable().row('#tr'+id_ref).remove().draw(false);
					Swal.fire('Cancellata!','La regola è stata eliminata.','success');
				} else {
					Swal.fire('Errore!', 'Si è verificato un problema durante la cancellazione.', 'error');
				}
			})
			.catch(err => {
				Swal.fire('Errore!', 'Si è verificato un errore di rete.', 'error');
				console.log(err);
			});
		}
	});
}

function edit_rule(id_ref) {
	$("#div_elenco").hide()
	window.Regole.reset_all()
	window.Regole.id_edit=id_ref
	pattern=$("#id_regola"+id_ref).data("pattern")
	
	inizia_con=$("#id_regola"+id_ref).data("inizia_con")
	min_len=$("#id_regola"+id_ref).data("min_len")
	max_len=$("#id_regola"+id_ref).data("max_len")
	len=$("#id_regola"+id_ref).data("len")
	
	inidbmod=$("#id_regola"+id_ref).data("inidbmod")
	if (inidbmod=="") inidbmod="-"
	inidbmod1=$("#id_regola"+id_ref).data("inidbmod1")
	if (inidbmod1=="") inidbmod1="-"

	edit_pattern=false;edit_inizia=false
	btn_pattern=false;btn_inizia_con=false
	if (pattern && pattern.length>0) {
		btn_pattern=true;btn_inizia_con=false
		edit_pattern=true;edit_inizia=false
	} 
	else {
		if (inizia_con && inizia_con.length>0) {
			btn_pattern=false;btn_inizia_con=true
			edit_pattern=false;edit_inizia=true
		}
	}	

	window.Regole.edit_pattern=edit_pattern
	window.Regole.edit_inizia=edit_inizia
	window.Regole.btn_pattern=btn_pattern
	window.Regole.btn_inizia_con=btn_inizia_con

	window.Regole.pattern=pattern
	window.Regole.inizia_con=inizia_con
	window.Regole.min_len=min_len
	window.Regole.max_len=max_len
	window.Regole.len=len

	window.Regole.DBmodelli=inidbmod
	window.Regole.DBmodelli1=inidbmod1
	window.Regole.DBmod=inidbmod
	window.Regole.DBmod1=inidbmod1	


	window.Regole.iniDBmodelli=inidbmod
	window.Regole.iniDBmodelli1=inidbmod1
	window.Regole.iniDBmod=inidbmod
	window.Regole.iniDBmod1=inidbmod1	
	/*
	$(".refr").hide()
	$("#div_refr"+id_ref).show()
	*/
	
}

function testcodice() {
	html=`
		<div style='width:20%' class="mt-3 mb-3 d-flex align-items-center">
		<strong role="status">Attendere. Verifica in corso</strong>
		<div class="spinner-border ms-auto" aria-hidden="true"></div>
		</div>	
	`	
	$("#div_resp_test").html(html)
	$("#div_resp_test").show(120)
	setTimeout(() => {
		//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
		const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
		const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
		test_codice=$("#test_codice").val();
		fetch("testcodice", {
			method: 'post',
			headers: {
				"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
				"X-CSRF-Token": csrf
			},
			body: "test_codice="+test_codice
		})
		.then(response => {
			if (response.ok) {
				return response.json();
			}
		})
		.then(resp=>{
			console.log(resp)
			pattern=resp.test_pattern
			inizia=resp.test_inizia
			num_regole=0
			reg_ptn=0
			reg_ini=0
			$.each( pattern, function( key, value ) {
				reg_ptn++
				num_regole++
			});			
			$.each( inizia, function( key, value ) {
				reg_ini++
				num_regole++
			});	
			html=""
			html+="<div class='mt-2'>"
				if (num_regole==0) {
					html+=`<div class="alert alert-warning" role="alert">
							<b>Attenzione!</b> Nessuna regola trovata per il codice
						</div>`
				}	
				if (num_regole==1) {
					html+=`<div class="alert alert-success" role="alert">
							<b>Ottimo!</b> Regola trovata per il codice
						</div>`
				}	

				if (num_regole>1) {
					html+=`<div class="alert alert-warning" role="alert">
							<b>Attenzione!</b> Conflitto di regole riscontrate.<hr>
							<b>N.B.</b><i> Attualmente viene applicata la regola con IDregola più alta</i>
						</div>`
				}

				if (reg_ptn>0) {
					//tabella regole trovate (Pattern)
					html+=`<table class="table table-striped">
							<tr>
								<th>IDregola</th>
								<th>Pattern</th>
								<th>DBmodelli</th>							
								<th>DBmodelli1</th>
								<th>Data ultima modifica</th>
							</tr>	
						`
						$.each( pattern, function( ptn, value ) {
							html+=`<tr>
									<td>`+value.id+`</td>
									<td>`+value.pattern+`</td>
									<td>`+value.DBmodelli+`</td>
									<td>`+value.DBmodelli1+`</td>
									<td>`+value.updated_at+`</td>
								</tr>
							`
						});	
					html+="</table>"
				}

				if (reg_ini>0) {
					//tabella regole trovate (Inizia con)
					html+=`<table class="mt-2 table table-striped">
							<tr>
								<th>IDregola</th>
								<th>Inizia con</th>
								<th>MinLen</th>
								<th>MaxLen</th>
								<th>Len</th>
								<th>DBmodelli</th>							
								<th>DBmodelli1</th>
								<th>Data ultima modifica</th>
							</tr>	
						`
						$.each( inizia, function( inizia_con, value ) {
							console.log("inizia_con",inizia_con)
							console.log("value",value)
							html+=`<tr>
									<td>`+value.id+`</td>
									<td>`+value.inizia_con+`</td>
									<td>`+value.min_len+`</td>
									<td>`+value.max_len+`</td>
									<td>`+value.len+`</td>
									<td>`+value.DBmodelli+`</td>
									<td>`+value.DBmodelli1+`</td>
									<td>`+value.updated_at+`</td>
								</tr>		
							`
						});	
					html+="</table>"
				}
				
			html+=`<button type="button" class="btn btn-secondary" onclick="$('#div_resp_test').empty;$('#div_resp_test').hide(120)">Chiudi Test</button>`
		

			html+="</div><hr>"

			$("#div_resp_test").html(html)		

		})
		.catch(err => {
			console.error("Errore durante il test del codice:", err);
		})	
	},300);
	
	
}