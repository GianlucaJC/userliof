$(document).ready( function () {
	imposta_app()
})  
function imposta_app() {

	var app = Vue.component('App',{
		template: 
		`<div class='container-fluid' mt-5 v-if="edit==true">
			<div class="row justify-content-center">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<h4>Modifica impostazione utente</h4>
						</div>
						<div class="card-body">
							<form @submit.prevent="validate">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control" id="operatore" placeholder="Nome operatore" v-model="operatore">
											<div v-if="errors['operatore']">
												{{ errors['operatore'] }}
											</div>
											<label for="operatore">Operatore</label>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control" id="userid" placeholder="UserID" v-model="userid">
											<div v-if="errors['user']">
												{{ errors['user'] }}
											</div>											
											<label for="userid">UserID</label>
										</div>
									</div>
									
									
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="password" class="form-control" id="password" placeholder="Password" v-model="password">
											<div v-if="errors['password']">
												{{ errors['password'] }}
											</div>
											<label for="password">Password</label>
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-4">
										<div class="form-group form-floating mb-3">
											<input type="text" class="form-control"  id="email" placeholder="Mail" v-model="email">
											<div v-if="errors['email']">
												{{ errors['email'] }}
											</div>
											<label for="email">Email</label>
										</div>
									</div>
								</div>

								<button class="btn btn-primary" :disabled='btn_edit_active' type="submit">Modifica</button>
								<button class="btn btn-secondary" type="button" @click="close_edit()">Esci</button>
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
				btn_edit_active,
				edit,
				operatore: '',
				userid:'',
				email: '',
				password: '',
				errors: []
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
					this.edit=true
					this.load_info(newval)
				}
			}	
		},		
		methods: {


			close_edit() {
				this.errors = [];
				this.id_user=0
				this.edit=false;
				document.getElementById('div_elenco').style.display = 'block';	
			},

			validate() {
				this.errors = [];
				var len = this.operatore.length;
				valid="form-control is-valid"
				invalid="form-control is-invalid"
				document.getElementById('operatore').className = valid;
				document.getElementById('userid').className = valid;
				document.getElementById('password').className = valid;
				document.getElementById('email').className = valid;

				// Operatore validate
				if (len < 5 || len > 20) {
					this.errors['operatore']="Il campo operatore deve essere compreso tra 5 e 20 caratteri."
				 	document.getElementById('operatore').className = invalid;
				}
				
				// userID validate
				var len = this.userid.length;
				if (len < 5 || len > 20) {
					this.errors['user']="Il campo userID deve essere compreso tra 5 e 20 caratteri."
					document.getElementById('userid').className = invalid;
				}	


				// pw validate
				var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!%*?&]{8,15}$/;
				if(this.password.match(regex) == null)  {
					this.errors['password']="Almeno un carattere minuscolo, almeno un carattere maiuscolo, almeno una cifra numerica, almeno un carattere speciale. Inoltre, la lunghezza totale deve essere compresa nell'intervallo [8-15]"
					document.getElementById('password').className = invalid;
				}	
				
				// email validate
				
				if (this.email.length>0) {
					var regex= /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					if(this.email.match(regex) == null) {
						this.errors['email']="Inserire una mail valida"
						document.getElementById('email').className = invalid;
				}
			}
		
			},
	
			emptyinfo() {
				this.operatore=""
				this.userid=""
				this.email=""
				this.password=""
			},

			load_info(id_user) {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				this.btn_edit_active=true
				this.emptyinfo();
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("load_info", {
					method: 'post',
					headers: {
					  "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
					  "X-CSRF-Token": csrf
					},
					body: "id_user="+id_user,
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
						this.email=resp[0].email

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