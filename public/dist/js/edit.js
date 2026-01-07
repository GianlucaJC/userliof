$(document).ready( function () {
	imposta_app()
})  
function imposta_app() {

	var app = Vue.component('App',{
		template: 
		`<div class='container-fluid' v-if="edit==true">
			<div class="row justify-content-center">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<h4>Modifica impostazione utente</h4>
						</div>
						<div class="card-body">
							<form @submit.prevent="validate" autocomplete="off">
								<!-- Campi fittizi per disabilitare l'autocompletamento della password del browser.
								     Sono posizionati fuori schermo invece di usare display:none per una maggiore compatibilità. -->
								<div style="position: absolute; top: -9999px; left: -9999px;">
									<input type="text" name="prevent_autofill" autocomplete="off">
									<input type="password" name="prevent_autofill_password" autocomplete="new-password">
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control" id="operatore" placeholder="Nome operatore" v-model="operatore" autocomplete="off">
											<div v-if="errors['operatore']">
												{{ errors['operatore'] }}
											</div>
											<label for="operatore">Operatore</label>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control" id="userid" placeholder="UserID" v-model="userid" autocomplete="off">
											<div v-if="errors['user']">
												{{ errors['user'] }}
											</div>											
											<label for="userid">UserID</label>
										</div>
									</div>
									
									
									<div class="col-md-4">
										<div class="form-group form-floating mb-3" v-show="showPasswordField">
											<input type="text" onfocus="this.type='password'" class="form-control" id="user_pass" name="user_pass" placeholder="Password" v-model="password" autocomplete="new-password">
											<div v-if="errors['password']">
												{{ errors['password'] }}
											</div>
											<label for="user_pass">Password</label>
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-12">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control"  id="email" placeholder="Mail" v-model="email">
											<div v-if="errors['email']">
												{{ errors['email'] }}
											</div>
											<label for="email">Email</label>
										</div>
									</div>
								</div>

								<hr>
								<h5>Permessi Applicativi</h5>
								<div class="row">
									<div class="col-md-3 mb-3">
										<label for="admin_lotti">Lotti</label>
										<select class="form-select" id="admin_lotti" v-model="admin_lotti">
											<option value="9">Disable</option>
											<option value="0">User</option>
											<option value="1">Admin</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="ruoli_cert" @click="showCertInfo" style="cursor: pointer;">Certificati <i class="fas fa-info-circle text-primary"></i></label>
										<select class="form-select" id="ruoli_cert" v-model="ruoli_cert">
											<option value="999">Disable</option>		
											<option value="1">Admin</option>
											<option value="2">2</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="admin_sos">SOS</label>
										<select class="form-select" id="admin_sos" v-model="admin_sos">
											<option value="9">Disable</option>	
											<option value="0">Manutentore</option>
											<option value="1">Utente</option>
											<option value="2">Utente/Viewer</option>
											<option value="10">Admin</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="rst_sos">Firma SOS</label>
										<select class="form-select" id="rst_sos" v-model="rst_sos">
											<option value="0">Standard</option>
											<option value="1">Responsabile Servizio Tecnico</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="admin_lp">Packing</label>
										<select class="form-select" id="admin_lp" v-model="admin_lp">
											<option value="10">Disable</option>
											<option value="1">Admin</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="admin_mp">Materie Prime</label>
										<select class="form-select" id="admin_mp" v-model="admin_mp">
											<option value="0">Disable</option>
											<option value="1">Admin</option>
											<option value="10">User</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="vest_access">Vestizione</label>
										<select class="form-select" id="vest_access" v-model="vest_access">
											<option value="">Disable</option>
											<option value="0">User</option>
											<option value="1">Admin</option>
										</select>
									</div>
									<div class="col-md-3 mb-3">
										<label for="nc_access" @click="showNcInfo" style="cursor: pointer;">Non Conformità <i class="fas fa-info-circle text-primary"></i></label>
										<select class="form-select" id="nc_access" v-model="nc_access">
											<option value="0">Disable</option>
											<option value="1">Admin</option>
											<option value="2">Segnalatore Base</option>
											<option value="3">Segnalatore Caporeparto</option>
											<option value="4">Valutatore</option>
											<option value="5">Eliminatore</option>
										</select>
									</div>
								</div>

								<hr>

								<button class="btn btn-primary" :disabled='btn_edit_active' type="submit">Modifica</button>
								<button class="btn btn-secondary" type="button" @click="close_edit()">Chiudi</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		`,

		
		data() {
			
			id_user=0;
			edit=false;
			btn_edit_active=true
			return {
				id_user,
				showPasswordField: false,
				btn_edit_active,
				edit,
				operatore: '',
				userid:'',
				email: '',
				password: '',
				admin_lotti: null,
                ruoli_cert: null,
                admin_sos: null,
                rst_sos: null,
                admin_lp: null,
                admin_mp: null,
                vest_access: null,
                nc_access: null,
				errors: {}
			}
		},
		mounted: function () {
			//this.set_validate()
			window.moduloEdit=this;
		},	
		watch:{
			id_user(newval,oldval) {
				console.log("newval",newval)
				if (newval!=0) {
					this.edit=true;
					this.load_info(newval);
					// Add listener when modal is active
					window.addEventListener('keydown', this.handleKeyDown);
				}
			}	
		},		
		methods: {

			showCertInfo() {
                Swal.fire({
                    title: 'Informazione sul Ruolo Cert',
                    html: "Per questa Applicazione (presto legacy), i ruoli erano determinati in base agli ID utenti (es: utente Rossi con ID 7 faceva determinate cose). <br><br>Ovviamente è difficile ora stabilire una descrizione di ruolo. Quindi per ora conviene (quando c'è la necessità) copiare il ruolo da altro utente del quale si conosce già il ruolo ricoperto.",
                    icon: 'info',
                    confirmButtonText: 'Ok'
                });
            },

			showNcInfo() {
                Swal.fire({
                    title: 'Informazione',
                    html: "I permessi per l'applicazione <b>Non Conformità</b> sono assegnabili (ad eccezione del privilegio <i>Admin</i>) anche dall'interno dell'applicazione stessa.",
                    icon: 'info',
                    confirmButtonText: 'Ok'
                });
            },

			handleKeyDown(e) {
				// Ctrl + Alt + P to toggle password visibility
				if (e.ctrlKey && e.altKey && (e.key === 'p' || e.key === 'P')) {
					e.preventDefault();
					this.showPasswordField = !this.showPasswordField;
				}
			},

			close_edit() {
				this.errors = [];
				this.id_user=0;
				this.edit=false;
				this.showPasswordField = false; // reset visibility
				// Remove listener when modal is closed
				window.removeEventListener('keydown', this.handleKeyDown);
				$('#modalvalue').modal('hide');
			},

			validate() {
				this.errors = {};
				var len = this.operatore.length;
				valid="form-control is-valid"
				invalid="form-control is-invalid"
				document.getElementById('operatore').className = valid;
				document.getElementById('userid').className = valid;
				if (this.showPasswordField) document.getElementById('user_pass').className = valid;
				document.getElementById('email').className = valid;

				// Operatore validate
				if (len > 30) {
					this.errors['operatore']="Il campo operatore deve essere meno di 30 caratteri."
				 	document.getElementById('operatore').className = invalid;
				}
				
				// userID validate
				var len = this.userid.length;
				if (len > 15) {
					this.errors['user']="Il campo userID deve essere meno di 15 caratteri."
					document.getElementById('userid').className = invalid;
				}	


				// pw validate
				var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!%*?&]{8,15}$/;
				/*
				if(this.password.match(regex) == null)  {
					this.errors['password']="Almeno un carattere minuscolo, almeno un carattere maiuscolo, almeno una cifra numerica, almeno un carattere speciale. Inoltre, la lunghezza totale deve essere compresa nell'intervallo [8-15]"
					if (this.showPasswordField) document.getElementById('user_pass').className = invalid;
				}	
				*/
				
				// email validate
				
				if (this.email.length>0) {
					var regex= /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					if(this.email.match(regex) == null) {
						this.errors['email']="Inserire una mail valida"
						document.getElementById('email').className = invalid;
				}
			}

				if (Object.keys(this.errors).length > 0) {
                    return false; // Stop if errors
                }
                this.save_user();
			},

			save_user() {
                const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";
                
                let formData = {
                    _token: csrf,
                    id_user: this.id_user,
                    operatore: this.operatore,
                    userid: this.userid,
                    password: this.password,
                    email: this.email,
                    admin_lotti: this.admin_lotti,
                    ruoli_cert: this.ruoli_cert,
                    admin_sos: this.admin_sos,
                    rst_sos: this.rst_sos,
                    admin_lp: this.admin_lp,
                    admin_mp: this.admin_mp,
                    vest_access: this.vest_access,
                    nc_access: this.nc_access
                };

                fetch("update_user", {
					method: 'POST',
					headers: {
					  "Content-Type": "application/json",
                      "Accept": "application/json",
					  "X-CSRF-Token": csrf
					},
                    body: JSON.stringify(formData)
				})
                .then(response => response.json())
                .then(data => {
                    if (data.response === 'OK' && data.user) {
                        Swal.fire({
							title: 'Aggiornato!',
							text: 'Utente aggiornato correttamente.',
							icon: 'success',
							timer: 1500,
							showConfirmButton: false
						});

						// Aggiorna la riga nella tabella dinamicamente
						var table = $('#tb_utenti').DataTable();
						var rowNode = table.row('#tr' + data.user.id).node();
						if (rowNode) {
							$(rowNode).find('td').eq(1).text(data.user.userid);
							$(rowNode).find('td').eq(2).html(`<i>${data.user.operatore}</i>`);
							$(rowNode).find('td').eq(3).text(data.user.email);
						}
						this.close_edit();
                    } else {
                        Swal.fire('Errore!', data.message || 'Si è verificato un errore durante il salvataggio.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Errore!', 'Si è verificato un errore di rete.', 'error');
                });
			},
	
			emptyinfo() {
				this.operatore=""
				this.userid=""
				this.email=""
				this.password=""
				this.admin_lotti = null;
				this.ruoli_cert = null;
				this.admin_sos = null;
				this.rst_sos = null;
				this.admin_lp = null;
				this.admin_mp = null;
				this.vest_access = null;
				this.nc_access = null;
			},

			load_info(id_user) {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				this.btn_edit_active=true
				this.emptyinfo();
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("load_info?id_user="+id_user, {
					method: 'get',
					headers: {
					  "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
					  "X-CSRF-Token": csrf
					},
				})
				.then(response => {
					if (response.ok) {
					   return response.json();
					}
				})
				.then(resp=>{
					if (resp[0]) {
						this.btn_edit_active=false
						this.operatore=resp[0].operatore
						this.userid=resp[0].userid
						this.password=resp[0].passkey
						this.email=resp[0].email || ''
						this.admin_lotti = resp[0].admin_lotti;
                        this.ruoli_cert = resp[0].ruoli_cert;
                        this.admin_sos = resp[0].admin_sos;
                        this.rst_sos = resp[0].rst_sos;
                        this.admin_lp = resp[0].admin_lp;
                        this.admin_mp = resp[0].admin_mp;
                        this.vest_access = resp[0].vest_access ?? '';
                        this.nc_access = resp[0].nc_access ?? 0;

						//this.resp=resp
					}
					
				})
				.catch(status, err => {
					return console.log(status, err);
				})	
			}		
		}	
	});

	let ev=new Vue ({
		el:"#app"
	});	
	
	
}