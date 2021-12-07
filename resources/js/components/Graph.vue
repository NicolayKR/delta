<script>
import { Line } from 'vue-chartjs'

export default {
  extends: Line,
  props: {
        chartData:{
            default:()=>{}
        },
        windowWidth:{
            default: null
        },
  },
  data(){
      return {
        option: {
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        fontFamily: "'Roboto',sans-serif",
                        fontColor: '#000'
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        fontFamily: "'Roboto',sans-serif",
                        fontColor: '#000'
                    }
                }]
            },
            tooltips: {
                display: true,
                bodyFontSize: 12
            },
            responseive : true ,
            maintainAspectRatio : false ,
            bezierCurve : false,
            animation : {
                duration : 500
            } ,
            hover : {
                animationDuration : 500
                },
            responseiveAnimationDuration : 500,
            onResize(chart, size) {
                chart.options.scales.xAxes[0].ticks.display = size.width >= 500;
                chart.update();
            },
            }
        }
    },
    watch:{
        chartData(){
           this.renderChart(this.chartData, this.option);
        },
    },
    mounted () {
        this.renderChart(this.chartData, this.option);
        if(this.windowWidth < 500){
            this.option.scales.xAxes[0].ticks.fontSize = 0;
            this.renderChart(this.chartData, this.option);
        }
    },
    methods:{

    }
}
</script>
