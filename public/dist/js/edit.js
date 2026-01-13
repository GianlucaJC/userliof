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
							<h4>Modifica utente: <span class="text-primary fw-bold">{{ operatore }}</span></h4>
						</div>
						<div class="card-body" style="overflow-y: auto; max-height: 75vh;">
							<form @submit.prevent="validate" autocomplete="off" id="editUserForm">
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
										<!-- Mostra campo password esistente solo per utenti già creati -->
										<div class="form-group form-floating mb-3" v-if="id_user !== 0" v-show="showPasswordField" title="Visibile con CTRL+ALT+P">
											<input type="text" onfocus="this.type='password'" class="form-control" id="user_pass" name="user_pass" placeholder="Password" v-model="password" autocomplete="new-password">
											<div v-if="errors['password']">
												{{ errors['password'] }}
											</div>
											<label for="user_pass">Password</label>
										</div>
										<!-- Mostra bottone "Modifica password" solo per utenti esistenti -->
										<button type="button" class="btn btn-outline-secondary mb-2" v-if="id_user !== 0" @click="toggleNewPassword">
											{{ showNewPasswordField ? 'Annulla modifica password' : 'Modifica password' }}
										</button>
										<!-- Mostra campo nuova password se si sta modificando o creando un utente da zero -->
										<div class="form-group form-floating" v-if="showNewPasswordField || (id_user === 0 && !is_external)">
											<input type="password" class="form-control" id="new_user_pass" placeholder="Nuova Password" v-model="new_password" autocomplete="new-password">
											<label for="new_user_pass">{{ (id_user === 0 && !is_external) ? 'Password (obbligatoria)' : 'Nuova Password' }}</label>
											<div v-if="errors['new_password']" class="text-danger small pt-1">
												{{ errors['new_password'] }}
											</div>
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

								<!-- Logica per la creazione di un utente interno da uno esterno -->
								<div v-if="!is_internal">
									<div class="form-check form-switch mb-3">
										<input class="form-check-input" type="checkbox" role="switch" id="createInternalSwitch" v-model="wants_to_create_internal">
										<label class="form-check-label" for="createInternalSwitch">Crea/Abilita utente anche su sistema interno</label>
									</div>

									<div v-if="wants_to_create_internal" class="mb-3">
										<label class="form-label fw-bold">Seleziona applicazioni interne da abilitare:</label>
										<div class="border rounded p-2">
											<div class="form-check" v-for="app in available_internal_apps" :key="app.id">
												<input class="form-check-input" type="checkbox" :value="app.id" :id="'int_app_' + app.id" v-model="selected_internal_apps">
												<label class="form-check-label" :for="'int_app_' + app.id">{{ app.name }}</label>
											</div>
										</div>
									</div>
								</div>

								<!-- Sezione Permessi Interni (visibile per utenti interni o durante la creazione) -->
								<div v-if="is_internal || wants_to_create_internal">
									<h5>Permessi Applicativi Interni</h5>
									<div class="row">
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('lotti')">
											<label for="admin_lotti">Lotti</label>
											<select class="form-select" id="admin_lotti" v-model="admin_lotti">
												<option value="9">Disable</option>
												<option value="0">User</option>
												<option value="1">Admin</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('certificati')">
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
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('sos')">
											<label for="admin_sos">SOS</label>
											<select class="form-select" id="admin_sos" v-model="admin_sos">
												<option value="9">Disable</option>	
												<option value="0">Manutentore</option>
												<option value="1">Utente</option>
												<option value="2">Utente/Viewer</option>
												<option value="10">Admin</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('sos')">
											<label for="rst_sos">Firma SOS</label>
											<select class="form-select" id="rst_sos" v-model="rst_sos">
												<option value="0">Standard</option>
												<option value="1">Responsabile Servizio Tecnico</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('packing')">
											<label for="admin_lp">Packing</label>
											<select class="form-select" id="admin_lp" v-model="admin_lp">
												<option value="10">Disable</option>
												<option value="1">Admin</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('materie_prime')">
											<label for="admin_mp">Materie Prime</label>
											<select class="form-select" id="admin_mp" v-model="admin_mp">
												<option value="0">Disable</option>
												<option value="1">Admin</option>
												<option value="10">User</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('vestizione')">
											<label for="vest_access">Vestizione</label>
											<select class="form-select" id="vest_access" v-model="vest_access">
												<option value="">Disable</option>
												<option value="0">User</option>
												<option value="1">Admin</option>
											</select>
										</div>
										<div class="col-md-3 mb-3" v-if="is_internal || selected_internal_apps.includes('nc')">
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
								</div>

								<hr>
								<h5>Permessi Applicativi Esterni</h5>

								<!-- Se l'utente NON è esterno, mostra lo switch per crearlo/abilitarlo -->
								<div v-if="!is_external">
									<div class="form-check form-switch mb-3">
										<input class="form-check-input" type="checkbox" role="switch" id="syncExternalSwitch" v-model="wants_to_sync_external">
										<label class="form-check-label" for="syncExternalSwitch">Crea/Abilita utente su sistema esterno</label>
									</div>

									<!-- Selezione delle APP esterne, mostrata solo se lo switch è attivo -->
									<div v-if="wants_to_sync_external" class="mb-3">
										<label class="form-label fw-bold">Seleziona applicazioni esterne da abilitare:</label>
										<div class="border rounded p-2">
											<div class="form-check" v-for="app in available_external_apps" :key="app.id">
												<input class="form-check-input" type="checkbox" :value="app.id" :id="'ext_app_' + app.id" v-model="selected_external_apps">
												<label class="form-check-label" :for="'ext_app_' + app.id">{{ app.name }}</label>
											</div>
										</div>
									</div>
								</div>
								<!-- Altrimenti, se l'utente è già esterno, mostra il box informativo -->
								<div v-else class="mb-3">
									<label class="form-label fw-bold">Applicazioni esterne abilitate:</label>
									<div class="border rounded p-2 bg-light">
										<p class="mb-0">APP Permessi</p>
									</div>
								</div>

								<!-- 3. Sezione dei permessi specifici per "APP Permessi" -->
								<div class="row" v-if="is_external || selected_external_apps.includes('permessi_app')">
									<div class="col-md-12">
										<fieldset class="border p-2 rounded" :disabled="!externalServiceAvailable">
											<legend class="float-none w-auto p-2 h6 mb-0">Permessi per "APP Permessi"</legend>
											<div class="row">
												<div class="col-md-4 mb-3">
													<label for="permessi_firma_cr">Firma CR</label>
													<select class="form-select" id="permessi_firma_cr" v-model="permessi_firma_cr">
														<option value="0">NO</option>
														<option value="1">SI</option>
													</select>
												</div>
												<div class="col-md-4 mb-3">
													<label for="permessi_firma_r">Firma R</label>
													<select class="form-select" id="permessi_firma_r" v-model="permessi_firma_r">
														<option value="0">NO</option>
														<option value="1">SI</option>
													</select>
												</div>
												<div class="col-md-4 mb-3">
													<label for="permessi_firma_d">Firma D</label>
													<select class="form-select" id="permessi_firma_d" v-model="permessi_firma_d">
														<option value="0">NO</option>
														<option value="1">SI</option>
													</select>
												</div>
											</div>
											<div class="row mt-3">
												<div class="col-12">
													<label class="mb-1">Reparti</label>
													<div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
														<div class="row">
															<div class="col-md-4 mb-1" v-for="reparto in reparti_options" :key="reparto.id">
																<div class="form-check">
																	<input class="form-check-input" type="checkbox" :value="reparto.id" :id="'reparto_' + reparto.id" v-model="permessi_reparti">
																	<label class="form-check-label" :for="'reparto_' + reparto.id">
																		{{ reparto.reparto }}
																	</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</fieldset>
									</div>
								</div>

							</form>
						</div>
						<div class="card-footer text-end">
							<button class="btn btn-primary" :disabled='isSaveButtonDisabled' type="submit" form="editUserForm">Salva</button>
							<button class="btn btn-secondary ms-2" type="button" @click="close_edit()">Chiudi</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		`,

		
		data() {
			
			id_user=0;
			edit=false;
			return {
				id_user,
				showPasswordField: false,
				showNewPasswordField: false,
				new_password: '',
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
				permessi_firma_cr: 0,
                permessi_firma_r: 0,
                permessi_firma_d: 0,
				permessi_reparti: [],
				reparti_options: [],
				externalServiceAvailable: true,
				external_passkey: '', // Memorizza la password (in chiaro) recuperata dal sistema esterno
				// --- Permessi Esterni ---
				available_external_apps: [{ id: 'permessi_app', name: 'APP Permessi' }],
				selected_external_apps: [],
				is_internal: false,
				is_external: false,
				wants_to_sync_external: false,
				// --- Permessi Interni (per creazione da esterno) ---
				wants_to_create_internal: false,
				selected_internal_apps: [],
				available_internal_apps: [
					{ id: 'lotti', name: 'Lotti' },
					{ id: 'certificati', name: 'Certificati' },
					{ id: 'sos', name: 'SOS (include Firma SOS)' },
					{ id: 'packing', name: 'Packing' },
					{ id: 'materie_prime', name: 'Materie Prime' },
					{ id: 'vestizione', name: 'Vestizione' },
					{ id: 'nc', name: 'Non Conformità' }
    			],
				errors: {}
			}
		},
		mounted: function () {
			//this.set_validate()
			window.moduloEdit=this;
			this.loadReparti();
		},
		computed: {
			isSaveButtonDisabled() {
				// Disabilita il pulsante se il form non è in modalità di modifica/creazione.
				// this.edit è il vero indicatore che un'operazione è in corso,
				// poiché this.id_user === 0 è ambiguo (usato sia per "nuovo utente" che per "nessun utente caricato").
				if (!this.edit) {
					return true;
				}

				// If APP Permessi is active (either because user is external or it's being enabled)
				const appPermessiEnabled = this.is_external || this.selected_external_apps.includes('permessi_app');
				
				// and no department is selected, then disable the button.
				if (appPermessiEnabled && this.permessi_reparti.length === 0) {
					return true;
				}

				// In all other cases, it's enabled.
				return false;
			}
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

			create_new() {
				this.emptyinfo();
				this.id_user = 0;
				this.edit = true;
				this.is_internal = false;
				this.is_external = false;
				this.password = ''; // Inutile per nuovo utente, ma pulito
				// Add listener for password shortcut
				window.addEventListener('keydown', this.handleKeyDown);
			},

			/**
			 * Carica i dati di un utente dal sistema esterno (permessi e password).
			 * NOTA: Si assume che l'endpoint API `get_user_permissions` restituisca
			 * anche la `passkey` in chiaro, come da specifica.
			 * @param {string} userid 
			 */
			loadExternalData(userid) {
				const apiUrl = 'https://www.liofilchemstore.it/servizi/api_login_ext.php';
				const apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';

				const formData = new FormData();
				formData.append('api_token', apiToken);
				formData.append('action', 'get_user_permissions');
				formData.append('userid', userid);

				return fetch(apiUrl, {
					method: 'POST',
					body: formData,
				})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'ok' && data.data) {
						const perms = data.data;
						this.permessi_firma_cr = perms.permessi_firma_cr ?? 0;
						this.permessi_firma_r = perms.permessi_firma_r ?? 0;
						this.permessi_firma_d = perms.permessi_firma_d ?? 0;
						
						// Parse reparto string 'R1R;R2R' into [1, 2]
						if (perms.reparto) {
							this.permessi_reparti = perms.reparto
								.split(';')
								.map(r => parseInt(r.replace(/R/g, '')))
								.filter(id => !isNaN(id));
						} else {
							this.permessi_reparti = [];
						}

						// Memorizza la password in chiaro proveniente dal sistema esterno
						this.external_passkey = perms.passkey || '';
					} else {
						// User might not exist externally, which is fine. Reset fields.
						this.permessi_firma_cr = 0;
						this.permessi_firma_r = 0;
						this.permessi_firma_d = 0;
						this.permessi_reparti = [];
						this.external_passkey = '';
						console.warn('Could not load external permissions for user:', userid, data.message);
					}
				})
				.catch(error => {
					console.error('Error fetching external permissions:', error);
					this.external_passkey = '';
				});
			},

			loadReparti() {
				this.externalServiceAvailable = true; // Si assume che il servizio sia disponibile all'inizio
				const apiUrl = 'https://www.liofilchemstore.it/servizi/api_login_ext.php';
				// NOTA DI SICUREZZA: Questo token è visibile nel codice sorgente del browser.
                // Per una maggiore sicurezza, la chiamata dovrebbe essere "proxata" dal backend.
                const apiToken = 'un-token-segreto-molto-sicuro-da-cambiare';

				const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 secondi di timeout

				const formData = new FormData();
                formData.append('api_token', apiToken);
                formData.append('action', 'get_reparti');

				fetch(apiUrl, { 
					method: 'POST',
                    body: formData,
					signal: controller.signal 
				})
					.then(response => {
						clearTimeout(timeoutId); // Annulla il timeout se la risposta arriva in tempo
						if (!response.ok) {
							throw new Error('Errore di rete o del server nel caricamento dei reparti.');
						}
						return response.json();
					})
					.then(data => {
						if (data.status === 'ok') {
							this.reparti_options = data.data;
						} else {
							this.externalServiceAvailable = false;
							console.error('Errore API reparti:', data.message);
							// Non mostriamo un popup bloccante per non interrompere il lavoro
						}
					})
					.catch(error => {
						clearTimeout(timeoutId); // Pulisce il timeout anche in caso di altri errori
						this.externalServiceAvailable = false;
						
						let message = 'Impossibile contattare il servizio per i permessi esterni. Le funzionalità interne sono comunque disponibili.';
						let title = 'Servizio Esterno Non Raggiungibile';

						if (error.name === 'AbortError') {
							message = 'Il servizio per i permessi esterni non risponde (timeout). Le funzionalità interne sono comunque disponibili.';
							title = 'Timeout Servizio Esterno';
							console.error('Timeout: La richiesta per i reparti esterni ha impiegato troppo tempo.');
						} else {
							console.error('Errore fetch reparti:', error);
						}

						// Mostra una notifica non bloccante (toast) invece di un alert modale
						Swal.fire({
							title: title,
							text: message,
							icon: 'warning',
							toast: true,
							position: 'top-end',
							showConfirmButton: false,
							timer: 5000,
							timerProgressBar: true
						});
					});
			},

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

			toggleNewPassword() {
				this.showNewPasswordField = !this.showNewPasswordField;
				if (!this.showNewPasswordField) {
					this.new_password = '';
					if (this.errors['new_password']) {
						delete this.errors['new_password'];
					}
				}
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
				this.showNewPasswordField = false;
				this.new_password = '';
				this.wants_to_sync_external = false;
				this.selected_external_apps = [];
				this.wants_to_create_internal = false;
				this.external_passkey = '';
				this.selected_internal_apps = [];
				// Remove listener when modal is closed
				window.removeEventListener('keydown', this.handleKeyDown);
				$('#modalvalue').modal('hide');
			},

			validate() {
				this.errors = {};
				const valid = "form-control is-valid";
				const invalid = "form-control is-invalid";
				let hasErrors = false;

				// Operatore validate
				if (!this.operatore || this.operatore.trim().length === 0) {
					this.errors['operatore'] = "Il campo operatore è obbligatorio.";
					document.getElementById('operatore').className = invalid;
					hasErrors = true;
				} else if (this.operatore.length > 30) {
					this.errors['operatore'] = "Il campo operatore deve essere meno di 30 caratteri.";
				 	document.getElementById('operatore').className = invalid;
					hasErrors = true;
				} else {
					document.getElementById('operatore').className = valid;
				}
				
				// userID validate
				if (!this.userid || this.userid.trim().length === 0) {
					this.errors['user'] = "Il campo userID è obbligatorio.";
					document.getElementById('userid').className = invalid;
					hasErrors = true;
				} else if (this.userid.length > 15) {
					this.errors['user'] = "Il campo userID deve essere meno di 15 caratteri.";
					document.getElementById('userid').className = invalid;
					hasErrors = true;
				} else {
					document.getElementById('userid').className = valid;
				}

				// new password validate
				const isNewUser = this.id_user === 0 && !this.is_external;
				const passField = document.getElementById('new_user_pass');
				if (passField && (this.showNewPasswordField || isNewUser)) {
					if (this.new_password.length === 0) {
						this.errors['new_password'] = isNewUser ? "La password è obbligatoria." : "La nuova password non può essere vuota.";
						passField.className = invalid;
						hasErrors = true;
					} else {
						passField.className = valid;
					}
				}
				
				// email validate
				if (this.email.length>0) {
					const regex= /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					if(this.email.match(regex) == null) {
						this.errors['email']="Inserire una mail valida"
						document.getElementById('email').className = invalid;
						hasErrors = true;
					} else {
						document.getElementById('email').className = valid;
					}
				} else {
					document.getElementById('email').className = valid; // Nullable is ok
				}

				if (hasErrors) {
                    // Mostra un avviso con il primo errore trovato, per coerenza con il comportamento atteso
                    const firstError = Object.values(this.errors)[0];
                    Swal.fire('Dati mancanti o non validi', firstError, 'error');
                    return false;
                }
                this.save_user();
			},

			save_user() {
                const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";
				
				let password_to_send;
				const isNewUser = this.id_user === 0 && !this.is_external;
				const isCreatingFromExternal = this.id_user === 0 && this.is_external;

				if (isCreatingFromExternal) {
					password_to_send = this.external_passkey;
				} else if (isNewUser || this.showNewPasswordField) {
					password_to_send = this.new_password;
				} else {
					password_to_send = this.password;
				}

                let formData = {
                    _token: csrf,
                    id_user: this.id_user,
                    operatore: this.operatore,
                    userid: this.userid,
                    password: password_to_send,
                    email: this.email,
                    admin_lotti: this.admin_lotti,
                    ruoli_cert: this.ruoli_cert,
                    admin_sos: this.admin_sos,
                    rst_sos: this.rst_sos,
                    admin_lp: this.admin_lp,
                    admin_mp: this.admin_mp,
                    vest_access: this.vest_access,
                    nc_access: this.nc_access,
					wants_to_sync_external: this.wants_to_sync_external,
					wants_to_create_internal: this.wants_to_create_internal
                };

				// Aggiungi i permessi esterni solo se il servizio è risultato disponibile
				if (this.externalServiceAvailable) {
					formData.permessi_firma_cr = this.permessi_firma_cr;
					formData.permessi_firma_r = this.permessi_firma_r;
					formData.permessi_firma_d = this.permessi_firma_d;
					formData.permessi_reparti = this.permessi_reparti;
				}

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
                    // Gestisce sia il successo completo (OK) che parziale (PARTIAL_OK)
                    if ((data.response === 'OK' || data.response === 'PARTIAL_OK') && data.user) {

                        // Mostra il messaggio appropriato
                        if (data.response === 'OK') {
                            Swal.fire({
                                title: 'Aggiornato!',
                                text: 'Utente aggiornato correttamente.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else { // PARTIAL_OK
                             Swal.fire({
                                title: 'Aggiornamento Parziale',
                                text: data.message, // Messaggio di avviso dal server
                                icon: 'warning',
                            });
                        }

						// Aggiorna la riga nella tabella dinamicamente
						var table = $('#tb_utenti').DataTable();
						var row = table.row('#tr' + data.user.id);
						if (row.length) {
							var rowNode = row.node();
							// Aggiorna il contenuto delle celle
							$(rowNode).find('td').eq(1).text(data.user.userid);
							$(rowNode).find('td').eq(2).html(`<i>${data.user.operatore}</i>`);
							$(rowNode).find('td').eq(3).text(data.user.email);
							// Invalida la riga per far ricalcolare l'ordinamento a DataTables e ridisegna
							row.invalidate().draw(false);
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
				this.new_password=""
				this.admin_lotti = null;
				this.ruoli_cert = null;
				this.admin_sos = null;
				this.rst_sos = null;
				this.admin_lp = null;
				this.admin_mp = null;
				this.vest_access = null;
				this.nc_access = null;
				this.permessi_firma_cr = 0;
				this.permessi_firma_r = 0;
				this.permessi_firma_d = 0;
				this.permessi_reparti = [];
				this.external_passkey = '';
				this.is_internal = false;
				this.selected_external_apps = [];
				this.is_external = false;
				this.wants_to_create_internal = false;
				this.selected_internal_apps = [];
				this.wants_to_sync_external = false;
			},

			load_info(userInfo) {
				this.emptyinfo();

				// Se userInfo è un oggetto, lo usiamo per pre-compilare il form per un nuovo utente.
				if (typeof userInfo === 'object' && userInfo !== null) {
					// Imposta l'ID a 0 (non triggera il watcher) e popola i dati.
					this.id_user = 0; 
					this.operatore = userInfo.name;
					this.userid = userInfo.username;
					this.email = userInfo.email;
					this.is_internal = false;
					this.is_external = true; // Proviene da un utente esterno
					this.edit = true; // Mostra il form
					this.password = ''; // Nessuna password da mostrare per un utente non ancora creato
					
					// Dato che l'utente esiste già esternamente, carichiamo i suoi permessi esterni.
					// I permessi interni rimangono vuoti/default grazie a emptyinfo().
					if (this.externalServiceAvailable) {
						this.loadExternalData(this.userid);
					}

					window.addEventListener('keydown', this.handleKeyDown);
					return; // Esce per non eseguire il fetch
				}

				// Altrimenti, userInfo è un id_user, carichiamo un utente esistente.
				const id_user = userInfo;
				if (!id_user) return;

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
						this.operatore = resp[0].operatore;
						this.userid = resp[0].userid;
						this.password = resp[0].passkey;
						this.email = resp[0].email || '';
						this.admin_lotti = resp[0].admin_lotti;
						this.ruoli_cert = resp[0].ruoli_cert;
						this.admin_sos = resp[0].admin_sos;
						this.rst_sos = resp[0].rst_sos;
						this.admin_lp = resp[0].admin_lp;
						this.admin_mp = resp[0].admin_mp;
						this.vest_access = resp[0].vest_access ?? '';
						this.nc_access = resp[0].nc_access ?? 0;
						
						this.is_internal = resp[0].is_internal;
						this.is_external = resp[0].is_external;
						this.wants_to_sync_external = false; // Reset on load

						// Carica i permessi esterni (sovrascrivendo eventuali cache locali)
						if (this.externalServiceAvailable) {
							this.loadExternalData(this.userid);
						}
					}
				})
				.catch(err => {
					console.error("Errore durante il caricamento delle informazioni utente:", err);
				})	
			}		
		}	
	});

	let ev=new Vue ({
		el:"#app"
	});	
	
	
}