import { createApp } from 'vue'


import regoleA from './components/regoleA.vue';
const app = createApp()

app.component('regoleA', regoleA);

app.mount('#app')