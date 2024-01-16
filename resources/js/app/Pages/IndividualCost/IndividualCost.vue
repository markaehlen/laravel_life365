<template>
  <div id="locations">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 py-1 px-2.5">
      <h1 class="font-semibold text-2xl">Individual Cost</h1>
    </div>
    <div class="bg-white overflow-hidden">
      <ValidationObserver ref="form">
        <form @submit.prevent="store">
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <div class="flex items-center">
              <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0">Concrete and Repair Unit Costs</h3>
            </div>
            <div class="flex">
              <inertia-link :href="route('service-life-report')" class="btn-indigo-flat mr-2"> Back </inertia-link>
              <inertia-link class="btn-indigo-flat" :href="route('life-cycle-cost')">
                <span>Next</span>
              </inertia-link>
            </div>
          </div>
          <div class="flex w-full flex-wrap">
            <div v-if="visibleGraph == 'life-cycle-cost'" class="py-1 px-2.5 flex flex-wrap w-full lg:w-1/3">
              <div class="w-full">
                <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                  <h3 class="font-semibold">Set Concrete Costs</h3>
                </div>
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-xs leading-normal">
                      <th class="p-1">Alternative</th>
                      <th class="p-1 text-center">$/{{ volume_unit }} (Click to edit)</th>
                      <th class="p-1 text-center">Is User Defined?</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-xs font-light">
                    <tr v-for="(set, index) in form.alternatives" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="p-1">
                        <span class="text-xs font-medium">{{ set.alternative.name }}</span>
                      </td>
                      <td class="p-1 text-center">
                        <span class="text-xs font-medium" v-if="set.alternative.userDefinedMixCost">
                          <inline-text-editor :min="0" :max="100000" title="Allowable range of values is [0, 100000]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!set.alternative.userDefinedMixCost" :value.sync="set.alternative.userMixCost"></inline-text-editor>
                        </span>
                        <span v-else class="text-xs font-medium"> {{ projectData.project.projectData.costs.baseMixCost }} </span>
                      </td>
                      <td class="p-1 text-center">
                        <span class="text-xs font-medium">
                          <input id="false" :value="false" :checked="set.alternative.userDefinedMixCost" class="p-2" v-model="set.alternative.userDefinedMixCost" type="checkbox" />
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-1 lg:px-40 lg:pb-5 -mr-5 -mb-2 w-full flex flex-wrap justify-center items-center">
                <loading-button :loading="form.processing" class="mx-10 btn-indigo-small mobile-back-btn" type="submit">Calculate Costs</loading-button>
              </div>
            </div>
            <div class="py-1 px-2.5 flex flex-wrap w-full lg:w-2/3 border-0 lg:border-l-4 md:border-l-4 border-solid border-gray-200 border-0 lg:border-b-4 md:border-b-4">
              <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                <h3 class="font-semibold">Required User Input</h3>
              </div>
              <p class="m-1 text-xs">To accurately capture the costs of your alternative concrete mix designs, you need to input the actual concrete costs of the mixes in your area. There are two types of mixes that need costs: (1) the basic mix, which has the concrete cost listed in the Default Concrete and Repair Costs tab above; and (2) an alternative mix that includes SCMs and inhibitors. If you have an alternative mix, you need to input the ready-mix cost of this concrete in the Set Concrete Costs table to the left.</p>
              <p class="m-1 text-xs">By default, the <b>Set Concrete Costs</b> table lists the cost of each mix design as the basic mix cost. To input the ready-mix cost of an alternative mix, click on the listed value of that cost in the center column of the table. If you need to reset this cost to the basic mix cost, uncheck the User box to the right of the cost.</p>
              <p class="m-1 text-xs"><b>Note:</b> Be sure to review the default values for important constituent costs, listed in the Default Concrete and Repair Costs tab above. Furthermore, if any of your concrete mixes include a membrane or sealer and you have inputed your own concrete cost to the left, Life-365™ will still add to that concrete cost the cost of the membrane or sealer.</p>
            </div>
          </div>
          <div class="flex w-full flex-wrap">
            <div class="py-1 px-2.5 flex flex-wrap w-full lg:w-1/3">
              <!-- <div class="mt-2 w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
                <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
                <h3 class="font-semibold">Alternatives</h3>
              </div> -->
              <hr />
              <div v-if="visibleGraph == 'life-cycle-cost'" class="p-1 -mb-2 flex flex-wrap w-full">
                <div class="w-full">
                  <div class="p-1 mb-2 flex flex-wrap justify-center items-center">
                    <select-input id="alternative" @input="calculateIndividualCosts()" v-model="activeAlternativeIndex" :required="true" :label="'Select Alternative'">
                      <option v-for="(set, index) in form.alternatives" :key="index" :value="index">{{ set.alternative.name }}</option>
                    </select-input>
                  </div>
                  <table class="w-full whitespace-nowrap text-left">
                    <thead>
                      <tr class="bg-gray-200 text-gray-600 text-xs leading-normal">
                        <th class="p-1">Item</th>
                        <th class="p-1 text-right">Quantity</th>
                        <th class="p-1 text-right">Amount ($)</th>
                      </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs font-light">
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Concrete Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(concrete_cost_quantity).toFixed(0) }} {{ volume_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(concrete_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Rebar Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(rebar_cost_quantity).toFixed(0) }} {{ weight_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(rebar_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Inhibitor Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(inhibitor_cost_quantity).toFixed(0) }} {{ capacity_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(inhibitor_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Construction Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(construction_cost_quantity).toFixed(0) }} {{ volume_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(construction_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Barrier Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(barrier_cost_quantity).toFixed(0) }} {{ area_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(barrier_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                      <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">Repair Cost</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">{{ parseFloat(repair_cost_quantity).toFixed(0) }} {{ area_unit }}</span>
                        </td>
                        <td class="p-1 text-right">
                          <span class="text-xs font-medium">${{ parseFloat(repair_cost_amount).toFixed(0) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="py-1 px-2.5 flex flex-wrap w-full lg:w-2/3 border-0 lg:border-l-4 md:border-l-4 border-solid border-gray-200">
              <div class="mt-2 w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                <h3 class="font-semibold">Project Costs for {{form.activeAlternativeName}}</h3>
              </div>
              <div v-if="visibleGraph == 'life-cycle-cost'" class="py-1 flex flex-wrap w-full">
                <div class="overflow-x-auto w-full">
                  <table class="w-full whitespace-nowrap text-left">
                    <thead>
                      <tr class="bg-gray-200 text-gray-600 text-xs leading-normal">
                        <th class="p-1">Cost Name</th>
                        <th class="p-1 text-center">Start Year</th>
                        <th class="p-1 text-center">End Year</th>
                        <th class="p-1 text-center">Interval</th>
                        <th class="p-1 text-center">Amount</th>
                        <th class="p-1 text-center">Units</th>
                        <th class="p-1 text-center">Area Unit</th>
                        <th class="p-1 text-center">Total</th>
                      </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs font-light">
                      <tr v-for="(set, index) in this.projectData.project.projectData.alts.alt[activeAlternativeIndex].alternative.costs" :key="index" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="p-1">
                          <span class="text-xs font-medium">{{ set.costName }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">{{ set.costTiming.startDate }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">{{ set.costTiming.endDate }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">{{ set.costTiming.frequency }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">{{ parseFloat(set.quantity).toFixed(0) }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">{{ set.unitOfMeasure }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">${{ parseFloat(set.unitCost).toFixed(0) }}</span>
                        </td>
                        <td class="p-1 text-center">
                          <span class="text-xs font-medium">${{ parseFloat(set.totalCost).toFixed(0) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                <h3 class="font-semibold">Cost Timeline for Alternative: {{form.activeAlternativeName}}</h3>
              </div>
              <div id="graphCanvas1" class="py-1 flex flex-wrap w-full">
                <Chart class="chart2 my-5" :key="uuid2" :indexAxis="`y`" type="bar" :xLabel="'Year'" :yLabel="`Costs`" title="Cost Timeline" :labels="createGraph2X()" :datasets="createGraph2Y()" :height="100" />
              </div>
            </div>
          </div>
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <div class="flex items-center">
              <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0"></h3>
            </div>
            <div class="flex">
              <inertia-link :href="route('concrete-mixtures')" class="btn-indigo-flat mr-2"> Back </inertia-link>
              <inertia-link class="btn-indigo-flat" :href="route('life-cycle-cost')">
                <span>Next</span>
              </inertia-link>
            </div>
          </div>
        </form>
      </ValidationObserver>
    </div>
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
import Chart from '@app/Shared/Chart'
import { uuid } from 'vue-uuid'
import VueHtml2pdf from 'vue-html2pdf'
import InlineTextEditor from '@app/Shared/InlineTextEditor'
import { EventBus } from '@app/event-bus'
import { Inertia } from '@inertiajs/inertia'

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
    Chart,
    VueHtml2pdf,
    InlineTextEditor,
  },
  layout: Layout,
  props: {
    projectData: Object,
    lccResults: Object,
    colors: [Object, Array],
    centimeterMetric: String,
    usUnits: String,
    siUnits: String,
    structures: Object,
  },
  computed: {},
  created() {},
  mounted() {
    this.getRedefinedUnits()
    this.calculateVolume(this.projectData.project.projectData.baseUnits, this.projectData.project.projectData.typeOfStructure, parseFloat(this.projectData.project.projectData.structureDimensions.trueThickness).toFixed(2), parseFloat(this.projectData.project.projectData.structureDimensions.area).toFixed(2))
    this.calculateIndividualCosts()
    EventBus.$on("navigateApplicationTo", (data) => {
      // window.location.replace(window.location.origin +'/'+ data);
      // window.location.replace($route(data));
      if(Array.isArray(data)){
        Inertia.visit(this.route(data[0],data[1]), { method: 'get' })
      }else{
        Inertia.visit(this.route(data), { method: 'get' })
      }
    });
  },
  data() {
    return {
      userAgent: this.$page.props.isMobile,
      visibleGraph: 'life-cycle-cost',
      secondaryVisibleGraph: 'constant-cost',
      defaultColDef: {
        flex: 1,
      },
      uuid1: uuid.v1(),
      uuid2: uuid.v1(),
      altColors: this.colors,
      activeAlternativeIndex: 0,
      volume_unit: null,
      area_unit: null,
      weight_unit: null,
      capacity_unit: null,
      length_unit: null,
      standard_length_unit: null,
      costList: null,
      structureVolume: null,

      // cost array

      concrete_cost_quantity: 0,
      concrete_cost_amount: 0,

      rebar_cost_quantity: 0,
      rebar_cost_amount: 0,

      inhibitor_cost_quantity: 0,
      inhibitor_cost_amount: 0,

      construction_cost_quantity: 0,
      construction_cost_amount: 0,

      barrier_cost_quantity: 0,
      barrier_cost_amount: 0,

      repair_cost_quantity: 0,
      repair_cost_amount: 0,

      yearBaseLine: null,

      //Form
      form: this.$inertia.form({
        baseUnits: this.projectData.project.projectData.baseUnits,
        trueThickness: parseFloat(this.projectData.project.projectData.structureDimensions.trueThickness).toFixed(2),
        clearCover: parseFloat(this.projectData.project.projectData.structureDimensions.clearCover).toFixed(2),
        structureAreaOrLength: parseFloat(this.projectData.project.projectData.structureDimensions.area).toFixed(2),

        alternatives: this.projectData.project.projectData.alts.alt,
        activeAlternativeName: this.projectData.project.projectData.alts.alt[0].alternative.name,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('calculate-individual-cost'), {
            onSuccess: () => this.$inertia.visit('/individual-cost'),
        })
    },
    getRedefinedUnits() {
      let baseUnits = this.projectData.project.projectData.baseUnits
      axios.get(`/change-units-string/${baseUnits}`).then((response) => {
        const { data } = response
        this.area_unit = data.area_unit
        this.volume_unit = data.volume_unit
        this.weight_unit = data.weight_unit
        this.capacity_unit = data.capacity_unit
        this.length_unit = data.length_unit
        this.standard_length_unit = data.standard_length_unit
      })
    },
    calculateIndividualCosts() {
      //reinitialisation of costs
      this.concrete_cost_quantity = 0
      this.concrete_cost_amount = 0

      this.rebar_cost_quantity = 0
      this.rebar_cost_amount = 0

      this.inhibitor_cost_quantity = 0
      this.inhibitor_cost_amount = 0

      this.construction_cost_quantity = 0
      this.construction_cost_amount = 0

      this.barrier_cost_quantity = 0
      this.barrier_cost_amount = 0

      this.repair_cost_quantity = 0
      this.repair_cost_amount = 0

      //recalculation of costs
      this.concrete_cost_quantity = this.structureVolume
      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.userDefinedMixCost) {
        this.concrete_cost_amount = this.structureVolume * this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.userMixCost
      } else {
        this.concrete_cost_amount = this.structureVolume * this.projectData.project.projectData.costs.baseMixCost
      }

      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.concreteMixDesign.rebarType) {
        this.rebar_cost_quantity = this.structureVolume * this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.concreteMixDesign.rebarType.percentOfTotal

        switch (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.concreteMixDesign.rebarType.type) {
          case 'Black Steel':
            this.rebar_cost_amount = this.rebar_cost_quantity * this.projectData.project.projectData.costs.blackSteel
            break
          case 'Epoxy Coated':
            this.rebar_cost_amount = this.rebar_cost_quantity * this.projectData.project.projectData.costs.epoxySteel
            break
          case '316 Stainless':
            this.rebar_cost_amount = this.rebar_cost_quantity * this.projectData.project.projectData.costs.stainlessSteel
            break
        }
      }

      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.concreteMixDesign.inhibitor !== '\u003cnone\u003e') {
        let extract_inhibitor = this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.concreteMixDesign.inhibitor.match(/\d/g)
        extract_inhibitor = extract_inhibitor.join('')

        this.inhibitor_cost_quantity = this.structureVolume * extract_inhibitor
        this.inhibitor_cost_amount = this.inhibitor_cost_quantity * this.projectData.project.projectData.costs.inhibitor
      }

      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs) {
        this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs.forEach((element, index) => {
          if (element.costName == 'Construction cost') {
            this.construction_cost_quantity = element.quantity
            this.construction_cost_amount = element.totalCost
          } else if (element.costName == 'Barrier cost') {
            this.barrier_cost_quantity = element.quantity
            this.barrier_cost_amount = element.totalCost
          } else {
            this.repair_cost_quantity = element.quantity
            this.repair_cost_amount = element.totalCost
          }
        })
      }
      this.getCostTimeLineGraph()
    },
    calculateVolume(baseUnits, typeOfStructure, trueThickness, areaOrLength) {
      let volume = null
      if (baseUnits === this.centimeterMetric) {
        trueThickness = trueThickness / 100
      } else if (baseUnits === this.siUnits) {
        trueThickness = trueThickness / 1000
      } else if (baseUnits === this.usUnits) {
        trueThickness = trueThickness / 12
      }

      if (typeOfStructure == 'slabs and walls (1-D)') {
        if (baseUnits === this.usUnits) {
          volume = trueThickness * areaOrLength * (1 / 27)
        } else {
          volume = trueThickness * areaOrLength
        }
      } else if (typeOfStructure == 'square column/beams (2-D)') {
        // 2D

        if (baseUnits === this.usUnits) {
          volume = (trueThickness / 3) * (trueThickness / 3) * areaOrLength * (1 / 3)
        } else {
          volume = trueThickness * trueThickness * areaOrLength
        }
      } else if (typeOfStructure == 'circular columns (2-D)') {
        // circ

        if (baseUnits === this.usUnits) {
          volume = Math.PI * Math.pow(trueThickness / 3 / 2, 2) * areaOrLength * (1 / 3)
        } else {
          volume = Math.PI * Math.pow(trueThickness / 2, 2) * areaOrLength
        }
      }
      this.structureVolume = parseFloat(volume).toFixed(2)
    },
    getCostTimeLineGraph() {
      this.uuid2 = uuid.v1()
    },
    createGraph2X() {
      let xAxis = []
      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs) {
        this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs.forEach((element, index) => {
          if (element.costName == 'Repair cost') {
            if (element.costTiming.frequency > 0) {
              xAxis.push([element.costName, '(At an interval of ' + element.costTiming.frequency + ' years )'])
            } else {
              xAxis.push(element.costName)
            }
          } else {
            xAxis.push(element.costName)
          }
        })
      }
      return xAxis
    },
    createGraph2Y() {
      let yAxis = []
      let yData = []

      let color = '#686A6C'

      if (this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs) {
        this.projectData.project.projectData.alts.alt[this.activeAlternativeIndex].alternative.costs.forEach((element, index) => {
          yData.push([element.costTiming.startDate !== '0' ? element.costTiming.startDate : '0', element.costTiming.endDate !== '0' ? element.costTiming.endDate : '1'])
        })
        yAxis.push({
          backgroundColor: color,
          data: yData,
        })
      }
      console.log(yData)
      console.log(yAxis)
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
