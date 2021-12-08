<template>
    <div>
        <div class="wrapper" v-if="this.flag == true">
            <h2 class="title">Общая статистика за данную неделю</h2>
            <div class = "graph-wrapper mt-4 mb-4">
                <graph :chartData ="datacollection" :windowWidth="windowWidth"/>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Показатель</th>
                        <th scope="col" v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{getDayName(String(date.date))}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-on:click="updateGraph('full_sum')" class="active">
                        <th scope="row">Выручка, руб</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{noEmpty(date.data, 'full_sum')}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('payment_types_1')">
                        <th scope="row">Наличные</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{noEmpty(date.data, 'payment_types_1')}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('payment_types_2')">
                        <th scope="row">Безналичный расчет</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{noEmpty(date.data, 'payment_types_2')}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('payment_types_3')">
                        <th scope="row">Кредитные карты</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{noEmpty(date.data, 'payment_types_3')}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('avg_rec')">
                        <th scope="row">Средний чек, руб</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.avg_rec}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('avg_guest')">
                        <th scope="row">Средний гость, руб</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.avg_guest}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('del_after')">
                        <th scope="row">Удаление из чека (после оплаты), руб</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.del_after}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('del_before')">
                        <th scope="row">Удаление из чека (до оплаты), руб</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.del_before}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('count_rec')">
                        <th scope="row">Количество чеков</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.count_rec}}</td>
                    </tr>
                    <tr v-on:click="updateGraph('count_guest')">
                        <th scope="row">Количество гостей</th>
                        <td v-for="(date,index) in this.data" :key = index :class="noEmpty(date.data, 'class')">{{date.data.count_guest}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="overlay">
            <div class="popup">
                <template v-if="error">
                    <div class="fw-bold" style="margin-right:10px">Произошла ошибка</div>
                </template>
                <template v-else>
                    <div class="fw-bold" style="margin-right:10px">Мы пытаемся получить данные</div>
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import graph from './Graph'
import moment from "moment";

export default {
    components: {
        graph,
    },
    data(){
        return{
            data: [],
            datacollection: null,
            windowWidth: window.innerWidth,
            params: 'full_sum',
            flag: false,
            error: false,
        }
    },
    mounted(){
        this.getData();
        this.getGraph();
        window.onresize = () => {
            this.windowWidth = window.innerWidth;
        }
    },
    methods:{
        async getData(){
            try{
                await axios.get(`/getData`).then(response => {
                    Object.entries(response.data).forEach(([key, value]) => {
                        let curr_date = {
                            'date':key,
                            'data':value
                        }
                        this.data.push(curr_date);
                    });
                    let overlay  = $('#overlay');
                    overlay.css('visibility','hidden');
                    overlay.css('opacity','0');
                    this.flag = true;
                });
            }
            catch{
                this.error = true;
                console.log('Ошибка');
            }
        },
        async getGraph(){
            try{
                await axios.get(`/getGraph?&params=${this.params}`).then(response => {
                    this.datacollection = response.data;
                });
            }
            catch{
                this.error = true;
                console.log('Ошибка');
            }
        },
        getDayName(day){
            var moment = require('moment');
            var today = String(moment().format('DD.MM.YYYY'));
            var yesterday = String(moment().subtract(1, 'days').format('DD.MM.YYYY'));
            switch(day){
                case today:
                    return 'Текущий день';
                case yesterday:
                    return 'Вчера';
                default:
                    return day;
            }
        },
        noEmpty(el, prop){
            if(el.hasOwnProperty(prop) == true){
                return el[prop];
            }else{
                return 0;
            }
        },
        updateGraph(par){
            this.params = par;
            this.getGraph();
        }
    }
}
</script>
