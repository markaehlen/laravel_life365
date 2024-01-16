<template>
  <canvas :id="id" :width="width" :height="height" />
</template>

<script>
import { Chart, registerables } from 'chart.js'
import { uuid } from 'vue-uuid'
Chart.register(...registerables)

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `chart-${this._uid}`
        // return uuid.v1()
      },
    },
    type: {
      type: String,
      default: 'line',
    },
    title: {
      type: String,
      default: 'Chart',
    },
    xLabel: {
      type: String,
      default: 'x-Axis',
    },
    yLabel: {
      type: String,
      default: 'y-Axis',
    },
    height: {
      type: Number,
      default: 200,
    },
    width: {
      type: Number,
      default: 600,
    },
    labels: {
      type: Array,
      default() {
        return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
      },
    },
    datasets: {
      type: Array,
      default() {
        return [
          {
            backgroundColor: 'rgba(99,179,237,0.4)',
            strokeColor: '#63b3ed',
            pointColor: '#fff',
            pointStrokeColor: '#63b3ed',
            data: [-203, 156, 99, 251, 305, 247, 256],
          },
          {
            backgroundColor: 'rgba(198,198,198,0.4)',
            strokeColor: '#f7fafc',
            pointColor: '#fff',
            pointStrokeColor: '#f7fafc',
            data: [-86, 97, 144, 114, 94, 108, 156],
          },
        ]
      },
    },
    options: {
      type: Object,
      default() {
        return {
          maintainAspectRatio: true,
          aspectRatio: 2,
          responsive: true,
          chartArea: {
            backgroundColor: 'rgba(251, 85, 85, 0.4)',
          },
          elements: {
            point: {
              pointRadius: 1,
            },
          },
          plugins: {
            legend: {
              display: this.type == 'bar' ? true : false,
            },
            title: {
              display: true,
              text: this.title,
            },
          },
          scales: {
            x: {
              stacked: this.type == 'bar' ? true : false,
              ticks: {
                beginAtZero: true,
                suggestedMax: 100,
                suggestedMin: -100,
              },
              title: {
                display: true,
                text: this.xLabel,
                font: {
                  size: 15,
                },
              },
            },
            y: {
              stacked: this.type == 'bar' ? true : false,
              ticks: {
                beginAtZero: true,
                suggestedMax: 100,
                suggestedMin: -100,
              },
              title: {
                display: true,
                text: this.yLabel,
                font: {
                  size: 15,
                },
              },
            },
          },
        }
      },
    },
  },
  data() {
    return {
      chartConfig: {
        type: this.type,
        data: {
          labels: this.labels,
          datasets: this.datasets,
        },
        options: this.options,
      },
    }
  },
  mounted() {
    new Chart(document.getElementById(this.id), this.chartConfig)
  },
}
</script>
