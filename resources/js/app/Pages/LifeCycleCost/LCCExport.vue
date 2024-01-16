<template>
  <div id="locations">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-1 mb-5">
      <h3 class="font-semibold">LCC Report Preview</h3>
      <button class="btn-indigo-small ml-auto mobile-back-btn" @click="generateReport()">Print Report</button>
    </div>
    <div class="overflow-scroll" style="text-align: -webkit-center; width:1px;height:1px">
      <vue-html2pdf
       class="w-full"
      :show-layout="true"
      :float-layout="false" 
      :filename="`LCC Report`" 
      :manual-pagination="true" 
      :enable-download="true" 
       ref="html2Pdf" 
       pdf-format="a4"
       >
        <section slot="pdf-content" >
          <div class="bg-white overflow-hidden p-2">
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mb-2">
              <h3 class="font-semibold">Life Cycle Costs Report</h3>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs lg:w-1/2">Project: {{ projectData.project.projectData.title }}</p>
              <p class="font-semibold text-xs lg:w-1/2">Description: {{ projectData.project.projectData.description }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs lg:w-1/2">Analyst: {{ projectData.project.projectData.doneByName }}</p>
              <p class="font-semibold text-xs lg:w-1/2">Date: {{ projectData.project.projectData.date }}</p>
            </div>
            
            <div class="w-full mt-2 border-b flex flex-nowrap items-center bg-gray-50 p-1 mb-2">
              <h3 class="font-semibold">Life Cycle Costs</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="w-full">
                <table class="whitespace-nowrap text-left" style="width: 98%;">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-1 px-6">Name</th>
                      <th class="py-1 px-6 text-center">Construction Cost</th>
                      <th class="py-1 px-6 text-center">Barrier Cost</th>
                      <th class="py-1 px-6 text-center">Repair Cost</th>
                      <th class="py-1 px-6 text-center">Life Cycle Cost</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in tableData" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-6">
                        <span class="text-xs font-medium">{{ set[0] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[1] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[2] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[3] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[4] }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Graphs</h3>
            </div>
            <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full">
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="'lccba-'+uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="`$`" title="Life Cycle Cost, by Alternative" :labels="createGraph2X()" :datasets="createGraph2Y()" />
              </div>
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="'cc-'+uuid2" type="bar" :xLabel="'Years'" :yLabel="`Constant Dollars ($)`" title="Constant Costs" :labels="createGraph4X()" :datasets="createGraph4Y()" />
              </div>
            </div>
            <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full">
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="uuid2" type="line" :xLabel="'Years'" :yLabel="`Constant Dollars ($)`" title="Cumulative Present Value" :labels="createGraph5X()" :datasets="createGraph5Y()" />
              </div>
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="uuid2" type="line" :xLabel="'Years'" :yLabel="`Current Dollars ($)`" title="Cumulative Current Costs" :labels="createGraph7X()" :datasets="createGraph7Y()" />
              </div>
            </div>
          </div>
          <br></br>
          <button class="btn-indigo-small ml-auto mobile-back-btn" @click="generateReport()">Print Report</button>
        </section>
      </vue-html2pdf>
    </div>
    <div>
    <div class="bg-white overflow-hidden m-1" style="max-width:800px;margin:0 auto;">
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mb-2">
              <h3 class="font-semibold">Life Cycle Costs Report</h3>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs lg:w-1/2">Project: {{ projectData.project.projectData.title }}</p>
              <p class="font-semibold text-xs lg:w-1/2">Description: {{ projectData.project.projectData.description }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs lg:w-1/2">Analyst: {{ projectData.project.projectData.doneByName }}</p>
              <p class="font-semibold text-xs lg:w-1/2">Date: {{ projectData.project.projectData.date }}</p>
            </div>
            
            <div class="w-full mt-2 border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Life Cycle Costs</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-1 px-6">Name</th>
                      <th class="py-1 px-6 text-center">Construction Cost</th>
                      <th class="py-1 px-6 text-center">Barrier Cost</th>
                      <th class="py-1 px-6 text-center">Repair Cost</th>
                      <th class="py-1 px-6 text-center">Life Cycle Cost</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in tableData" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-6">
                        <span class="text-xs font-medium">{{ set[0] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[1] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[2] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[3] }}</span>
                      </td>
                      <td class="py-1 px-6 whitespace-nowrap text-center">
                        <span class="text-xs font-medium">{{ set[4] }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Graphs</h3>
            </div>
            <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full">
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full lg:w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="'lccba-'+uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="`$`" title="Life Cycle Cost, by Alternative" :labels="createGraph2X()" :datasets="createGraph2Y()" />
              </div>
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full lg:w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="'cc-'+uuid2" type="bar" :xLabel="'Years'" :yLabel="`Constant Dollars ($)`" title="Constant Costs" :labels="createGraph4X()" :datasets="createGraph4Y()" />
              </div>
            </div>
            <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full">
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full lg:w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="uuid2" type="line" :xLabel="'Years'" :yLabel="`Constant Dollars ($)`" title="Cumulative Present Value" :labels="createGraph5X()" :datasets="createGraph5Y()" />
              </div>
              <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full lg:w-1/2">
                <Chart class="chart2 m-5" :apRatio="1" :key="uuid2" type="line" :xLabel="'Years'" :yLabel="`Current Dollars ($)`" title="Cumulative Current Costs" :labels="createGraph7X()" :datasets="createGraph7Y()" />
              </div>
            </div>
          </div>
    </div>
    <br></br>
    <button class="btn-indigo-small ml-auto mobile-back-btn" @click="generateReport()">Print Report</button>
  </div>
  
</template>

<script>
import swal from 'sweetalert'
import Icon from '@app/Shared/Icon'
import Layout from '@app/Shared/Layout'
import TextInput from '@app/Shared/TextInput'
import NumberInput from '@app/Shared/NumberInput'
import ActionBack from '@app/Shared/ActionBack'
import TextareaInput from '@app/Shared/TextareaInput'
import LoadingButton from '@app/Shared/LoadingButton'
import SelectInput from '@app/Shared/SelectInput.vue'
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import { AgGridVue } from 'ag-grid-vue'
import Chart from '@app/Shared/Chart'
import { uuid } from 'vue-uuid'
import VueHtml2pdf from 'vue-html2pdf'
import { EventBus } from '@app/event-bus'
export default {
  metaInfo: { title: 'Life Cycle Cost' },
  components: {
    Icon,
    pickBy,
    throttle,
    TextInput,
    NumberInput,
    SelectInput,
    ActionBack,
    LoadingButton,
    TextareaInput,
    VSwatches,
    AgGridVue,
    Chart,
    VueHtml2pdf,
  },
  layout: Layout,
  props: {
    projectData: Object,
    lccResults: Object,
    colors: [Object, Array],
  },
  computed: {},
  created() {},
  mounted() {
    EventBus.$on("navigateApplicationTo", (data) => {
      // window.location.replace(window.location.origin +'/'+ data);
      if(Array.isArray(data)){
        this.$inertia.visit(this.route(data[0],data[1]), { method: 'get' })
      }else{
        this.$inertia.visit(this.route(data), { method: 'get' })
      }
    });
  },
  data() {
    return {
      visibleGraph: 'life-cycle-cost',
      secondaryVisibleGraph: 'constant-cost',
      defaultColDef: {
        flex: 1,
      },
      uuid1: uuid.v1(),
      uuid2: uuid.v1(),
      altColors: this.colors,
      activeAlternativeIndex: 0,
      tableData: this.lccResults.results.LCCTable.lccSummaryTable.dataVector,
      constantGraphData: this.lccResults.results.constant.dataset,
      cumConstantGraphData: this.lccResults.results.cumConstant.dataset,
      currentGraphData: this.lccResults.results.current.dataset,
      cumCurrentGraphData: this.lccResults.results.cumCurrent.dataset,
    }
  },
  methods: {
    generateReport() {
      this.$refs.html2Pdf.generatePdf()
    },
    preparePlayground() {
      return true
    },
    toggleGraphTab(graphName) {
      this.visibleGraph = graphName
      this.uuid2 = uuid.v1()
    },
    toggleSecondaryGraphTab(graphName) {
      this.secondaryVisibleGraph = graphName
      this.uuid2 = uuid.v1()
    },
    resetDefaults() {
      this.$store.state.currentSystem = null
      this.$store.state.ecoParameters = null
      window.location.replace('/reset-defaults')
    },
    setActiveAlternative(index) {
      this.activeAlternativeIndex = index
      this.form.activeAlternativeName = this.projectData.project.projectData.alts.alt[index].alternative.name
      this.form.activeAlternativeDesc = this.projectData.project.projectData.alts.alt[index].alternative.description
    },
    getRedefinedUnits(value, eps) {
      this.$store.state.ecoParameters = eps
      axios
        .post(`/redefine-units`, {
          current_system_identifier: this.$store.state.currentSystem,
          new_system_identifier: value,
          data: eps,
        })
        .then((response) => {
          const { data } = response
          //Storing to state
          this.$store.state.currentSystem = data.current_system
          this.$store.state.ecoParameters = data.ecoparameters
          //Setting data values
          this.concentrationUnitsOptions = data.conc_units
          this.area_unit = data.area_unit
          this.volume_unit = data.volume_unit
          this.weight_unit = data.weight_unit
          this.capacity_unit = data.capacity_unit
          //Setting form values
          this.form.concUnits = data.default_conc_unit
          this.form.ecoparameters = data.ecoparameters
          this.form.baseYear = data.ecoparameters.baseYear.value
          this.form.studyPeriod = data.ecoparameters.studyPeriod.value
          this.form.inflation = parseFloat(data.ecoparameters.inflation.value).toFixed(2)
          this.form.discount = parseFloat(data.ecoparameters.discount.value).toFixed(2)
          this.form.baseMixCost = parseFloat(data.ecoparameters.baseMixCost.value).toFixed(2)
          this.form.costBlackSteel = parseFloat(data.ecoparameters.costBlackSteel.value).toFixed(2)
          this.form.costEpoxy = parseFloat(data.ecoparameters.costEpoxy.value).toFixed(2)
          this.form.costStainless = parseFloat(data.ecoparameters.costStainless.value).toFixed(2)
          this.form.costMembrane = parseFloat(data.ecoparameters.costMembrane.value).toFixed(2)
          this.form.costSealer = parseFloat(data.ecoparameters.costSealer.value).toFixed(2)
          this.form.costInhibitor = parseFloat(data.ecoparameters.costInhibitor.value).toFixed(2)
          this.form.repairCost = parseFloat(data.ecoparameters.repairCost.value).toFixed(2)
          this.form.areaToRepairPct = parseFloat(data.ecoparameters.areaToRepairPct.value).toFixed(2)
          this.form.repairIntervalYrs = data.ecoparameters.repairIntervalYrs.value
        })
    },
    getSublocations(value) {
      axios.get(`/get-sublocations/${value}`).then((response) => {
        const { data } = response
        this.sublocationOptions = data.sublocations
        this.form.sublocation = data.default_value
        this.getExposures()
      })
    },
    getExposures() {
      axios.get(`/get-exposures/${this.form.subLocation}`).then((response) => {
        const { data } = response
        this.exposureOptions = data.exposures
        this.form.exposureType = data.default_value
      })
    },
    validateAndUpdateParams(parameter, field, entry, min, max) {
      if (entry && entry > max) {
        swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
        entry = max
      } else if (entry && entry < min) {
        swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
        entry = min
      }
    },
    onGridReady(params) {
      this.gridApi = params.api
      this.gridColumnApi = params.columnApi

      params.api.sizeColumnsToFit()
      window.addEventListener('resize', function () {
        setTimeout(function () {
          params.api.sizeColumnsToFit()
        })
      })

      params.api.sizeColumnsToFit()
    },
    createGraph2X() {
      let xAxis = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        xAxis.push(this.projectData.project.projectData.alts.alt[i].alternative.name + ' = ' + this.tableData[i][4])
      }
      return xAxis
    },
    createGraph2Y() {
      let yAxis = []
      let lifeCycleCost = []
      let bgColorList = []
      //insert lifeCycleCost
      for (let i = 0; i < this.tableData.length; i++) {
        lifeCycleCost.push(this.tableData[i][4].replace(/\$/g, '').replace(/,/g, ''))
        bgColorList.push(this.altColors[i])
      }
      yAxis.push({
        label: 'Life Cycle Cost',
        backgroundColor: bgColorList,
        data: lifeCycleCost,
      })
      return yAxis
    },
    createGraph3X() {
      let xAxis = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        xAxis.push(this.projectData.project.projectData.alts.alt[i].alternative.name)
      }
      return xAxis
    },
    createGraph3Y() {
      let yAxis = []
      let constructionCost = []
      let repairCost = []
      //insert initiationYears
      let iniColor = '#808080'
      let propColor = '#C0C0C0'
      for (let i = 0; i < this.tableData.length; i++) {
        constructionCost.push(this.tableData[i][1].replace(/\$/g, '').replace(/,/g, ''))
      }
      yAxis.push({
        label: 'Construction Cost',
        backgroundColor: iniColor,
        data: constructionCost,
      })

      for (let i = 0; i < this.tableData.length; i++) {
        repairCost.push(this.tableData[i][2].replace(/\$/g, '').replace(/,/g, ''))
      }
      yAxis.push({
        label: 'Repair Cost',
        backgroundColor: propColor,
        data: repairCost,
      })

      return yAxis
    },
    createGraph4X() {
      let xAxis = []
      Object.entries(this.constantGraphData[this.projectData.project.projectData.alts.alt[0].alternative.name]).forEach(([key, value]) => xAxis.push(key))
      return xAxis
    },
    createGraph4Y() {
      let yAxis = []
      for (let i = 0; i < Object.keys(this.constantGraphData).length; i++) {
        let values = []
        Object.entries(this.constantGraphData[this.projectData.project.projectData.alts.alt[i].alternative.name]).forEach(([key, value]) => values.push(value))
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[0].alternative.name,
          borderColor: this.altColors[i],
          backgroundColor: this.altColors[i],
          fill:true,
          data: values,
        })
      }
      return yAxis
    },
    createGraph5X() {
      let xAxis = []
      Object.entries(this.cumConstantGraphData[this.projectData.project.projectData.alts.alt[0].alternative.name]).forEach(([key, value]) => xAxis.push(key))
      return xAxis
    },
    createGraph5Y() {
      let yAxis = [];

      for (let i = 0; i < Object.keys(this.cumConstantGraphData).length; i++) {
        let values = [];
        Object.entries(this.cumConstantGraphData[this.projectData.project.projectData.alts.alt[i].alternative.name]).forEach(([key, value]) => values.push(value))  
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          pointRadius: 0,
          borderColor: this.altColors[i],
          data: values,
        })
      }

      return yAxis
    },
    createGraph6X() {
      let xAxis = []
      Object.entries(this.currentGraphData[this.projectData.project.projectData.alts.alt[0].alternative.name]).forEach(([key, value]) => xAxis.push(key))
      return xAxis
    },
    createGraph6Y() {
      let yAxis = []

      for (let i = 0; i < Object.keys(this.currentGraphData).length; i++) {
        let values = []
        Object.entries(this.currentGraphData[this.projectData.project.projectData.alts.alt[i].alternative.name]).forEach(([key, value]) => values.push(value))
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          data: values,
        })
      }

      return yAxis
    },
    createGraph7X() {
      let xAxis = []
      Object.entries(this.cumCurrentGraphData[this.projectData.project.projectData.alts.alt[0].alternative.name]).forEach(([key, value]) => xAxis.push(key))
      return xAxis
    },
    createGraph7Y() {
      let yAxis = []
      let legendList = []

      for (let i = 0; i < Object.keys(this.cumCurrentGraphData).length; i++) {
        let values = []
        Object.entries(this.cumCurrentGraphData[this.projectData.project.projectData.alts.alt[i].alternative.name]).forEach(([key, value]) => values.push(value))
        legendList.push(this.projectData.project.projectData.alts.alt[i].alternative.name)
        yAxis.push({
          label: 'Cum Current Cost ($)',
          pointRadius: 0,
          legend: this.projectData.project.projectData.alts.alt[i].alternative.name,
          borderColor: this.altColors[i],
          data: values,
        })
      }

      return yAxis
    },
  },
}
</script>

<style scoped>
.chart2 {
  background-color: #f5f5f5;
  padding: 10px;
}
.topnav {
  overflow: hidden;
  background-color: #333;
  margin: 20px;
}

.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #7886d7;
  color: white;
}

.topnav .icon {
  display: none;
}

@media screen and (max-width: 600px) {
  .topnav a:not(:first-child) {
    display: none;
  }
  .topnav a.icon {
    float: right;
    display: block;
  }
}

@media screen and (max-width: 600px) {
  .topnav.responsive {
    position: relative;
  }
  .topnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}
</style>
