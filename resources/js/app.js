require('./bootstrap');
require('./script');
import Vue from "vue";
import VueCharts from 'vue-chartjs';

Vue.use(VueCharts);
Vue.component('table-component', require('./components/Table').default);

new Vue({
    el: '#app',
});
