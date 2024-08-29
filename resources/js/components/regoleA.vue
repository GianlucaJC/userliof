<template>

	
									
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
									<button class="btn btn-secondary ml-2" type="button" @click="btn_pattern=false;btn_inizia_con=false;savepattern=false">Chiudi sezione Pattern</button>
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
									Si è verificato un problema durante il salvataggio della stringa inizia con!
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
									<button class="btn btn-secondary ml-2" type="button" @click="btn_pattern=false;btn_inizia_con=false;saveinizia=false">Chiudi sezione Inizia con</button>
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
  </template>
  
  <script>
  export default {
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
			.catch(status, err => {
				return console.log(status, err);
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
		    if (!confirm("Sicuri di salvare la regola?")) return false;
			//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
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
				console.log(resp)
				console.log("resp.header",resp.header)
				
				if (resp.header=='OK') {
					this.saveinizia=true
					this.saveinizia_esito=true
					//athis.emptyinfo();	
				} else {
					this.saveinizia=true
					this.saveinizia_esito=false
				}
					


			})
			.catch(status, err => {
				return console.log(status, err);
			})	
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
		    if (!confirm("Sicuri di salvare la regola?")) return false;
			//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
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
				console.log(resp)
				console.log("resp.header",resp.header)
				if (resp.header=='OK') {
					this.savepattern=true
					this.savepattern_esito=true
					this.emptyinfo();	
				} else {
					this.savepattern=true
					this.savepattern_esito=false
				}

			})
			.catch(status, err => {
				return console.log(status, err);
			})	
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
  }
  </script>
  
<style>

</style>