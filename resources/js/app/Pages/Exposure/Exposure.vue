<template>
  <div id="locations">
    <form @submit.prevent="store">
    <div class="flex w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 py-1 px-2.5">
      <h1 class="font-semibold text-2xl">Project Exposure Details</h1>
      <div class="flex">
              <inertia-link :href="route('set-project')" class="btn-indigo-flat mr-2"> Back </inertia-link>
              <loading-button id="save-button" :loading="form.processing" class="btn-indigo-flat mr-2" type="submit">Next</loading-button>
      </div>
    </div>
    <div class="bg-white overflow-hidden">
      
        
        <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0">Select Method for Setting External Concentration and Temperature Profile</h3>
            
          </div>
        <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
          <input id="false" @change="renderBothGraphs()" :value="false" :checked="!form.setManually" class="p-2" v-model="form.setManually" type="radio" />
          <label class="p-1 font-semibold">Use Defaults</label>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-4 p-2.5">
          <select-input :disabled="form.setManually" id="location" @input="getSublocations($event)" v-model="form.location" :tool-tip-text="helping_tips.exposure_location" :error="form.errors.location" class="block" :required="true" :label="'Location'">
            <option v-for="(location, index) in locationOptions" :key="index" :value="index">{{ location }}</option>
          </select-input>
          <select-input :disabled="form.setManually" id="sub-location" @input="getExposures($event)" v-model="form.subLocation" :tool-tip-text="helping_tips.exposure_sub_location" :error="form.errors.subLocation" class="block" :required="true" :label="'Sub-locations'">
            <option v-for="(sublocation, index) in sublocationOptions" :key="index" :value="index">{{ sublocation }}</option>
          </select-input>
          <select-input :disabled="form.setManually" id="exposure" @input="getConcAndTemperatures()" v-model="form.exposureType" :tool-tip-text="helping_tips.exposure_exposure" :error="form.errors.exposureType" class="block" :required="true" :label="'Exposure'">
            <option v-for="(exposure, index) in exposureOptions" :key="index" :value="index">{{ exposure }}</option>
          </select-input>
        </div>
        <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 px-2.5">
          <input id="true" @change="renderBothGraphs()" :value="true" :checked="form.setManually" @input="updateMaxCSAndTimeToMaxBasedOnASTM(form.c1556SetUsed)" class="p-2" v-model="form.setManually" type="radio" />
          <label class="p-1 font-semibold">Set Values Manually</label>
        </div>
        <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
          <h3 class="font-semibold">Chloride Exposure ({{ form.setManually ? 'User defined' : 'Automatically set' }})</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 md:grid-cols-2 gap-6 p-2.5">
          <div class="leftBlockGrid">
            <span class="pb-2 block"><strong>Max Concentration</strong></span>
            <span class="mb-8">
              <input :disabled="!form.setManually" type="radio" id="yes" :value="false" v-model="form.useC1556" />
            </span>
            <span class="block">
              <number-input :rules="`required|min_value:0|max_value:1000`" :title="`Allowable range of values is [0 , 1000]`" @input="renderGraph1(form.maxSurfaceConcentration)" :disabled="!form.setManually || form.useC1556" v-model="form.maxSurfaceConcentration" step="any" :error="form.errors.maxSurfaceConcentration" :required="true" class="block" :label="'Manual (' + $page.props.concUnits + ')'" />
            </span>
            <span class="mb-8 w-full lg:w-1/6 text-center self-center"><input type="radio" id="yes" :value="true" v-model="form.useC1556" @input="updateMaxCSAndTimeToMaxBasedOnASTM(form.c1556SetUsed)" :disabled="!form.setManually" /></span>
            <span class="block pb-2"
              ><select-input :disabled="!form.setManually || !form.useC1556" @input="updateMaxCSAndTimeToMaxBasedOnASTM(form.c1556SetUsed)" v-model="form.c1556SetUsed" :tool-tip-text="helping_tips.exposure_mannual" :error="form.errors.c1556Sets" class="block" :required="true" :label="'ASTM C1556 (' + $page.props.concUnits + ')'">
                <option v-for="(c1556Set, index) in form.c1556Sets" :key="index" :value="c1556Set.name">{{ c1556Set.name }}</option>
              </select-input>
            </span>

            <span v-show="form.setManually && form.useC1556" class="pb-2 w-full">
              <span :disabled="!form.setManually || !form.useC1556" @click="toggleASTMWindow()" class="btn-indigo-small ml-auto astm-buttons">Add New</span>
              <span :disabled="!form.setManually || !form.useC1556 || !form.c1556SetUsed" @click="editASTMWindow()" class="btn-indigo-small btnsmall ml-auto astm-buttons">Edit Set</span>
              <span :disabled="!form.setManually || !form.useC1556 || !form.c1556SetUsed" class="btn-indigo-small ml-auto astm-buttons" @click="deleteASTMSet()">Delete</span>
            </span>
            <span class="pr-5 pb-2 block"><strong>Time to Max</strong></span>
            <number-input @input="renderGraph1(form.timeToBuildUp)" :rules="`required|min_value:0|max_value:1000`" :title="`Allowable range of values is [0 , 1000]`" :disabled="!form.setManually" v-model="form.timeToBuildUp" step="any" :tool-tip-text="helping_tips.exposure_time_to_max" :error="form.errors.timeToBuildUp" :required="true" class="block" :label="'Years to build to max surface concentration'" />
          </div>
          <div id="graphCanvas1" class="rightBlockGraph">
            <Chart class="chart1" :key="uuid1" type="line" :apRatio="$page.props.isMobile? 1.5 : 2" :title="'Surface Concentration'" :xLabel="'Year'" :yLabel="$page.props.concUnits" :labels="createGraph1X()" :datasets="createGraph1Y()" />
          </div>
        </div>
        <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
          <h3 class="font-semibold">Temperature Cycle ({{ temperature_unit }}) ({{ form.setManually ? 'User defined' : 'Automatically set' }})</h3>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 md:grid-cols-2 gap-6 p-2.5">
          <div class="leftBlockGrid">
            <div class="border-gray-200 w-full">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-1 px-3">Month</th>
                      <th class="py-1 px-3 text-right">Temperature ({{ temperature_unit }} )</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">January</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.jan_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">February</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.feb_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">March</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.mar_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">April</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.apr_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">May</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.may_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">June</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.jun_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">July</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.jul_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">August</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.aug_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">September</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.sep_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">October</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.oct_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">November</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.nov_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-1 px-3">
                        <span class="text-sm font-medium text-gray-900">December</span>
                      </td>
                      <td class="py-1 px-3 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">
                          <inline-text-editor :min="-45.6" :max="65.6" title="Allowable range of values is [-45.6, 65.6]" :required="true" :close-on-blur="true" :type="'number'" :disabled="!form.setManually" :value.sync="form.dec_temp" @update="renderGraph2()"></inline-text-editor>
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div id="graphCanvas2" class="rightBlockGraph">
            <Chart class="chart2" :key="uuid2" type="line" :apRatio="$page.props.isMobile? 1.5 : 2" :xLabel="'Month'" :yLabel="`Temperature (${temperature_unit})`" title="Temperature vs Calendar Month Graph" :labels="createGraph2X()" :datasets="createGraph2Y()" />
          </div>
        </div>
        <div class="flex w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0"></h3>
            <div class="flex">
              <inertia-link :href="route('set-project')" class="btn-indigo-flat mr-2"> Back </inertia-link>
              <loading-button id="save-button" :loading="form.processing" class="btn-indigo-flat mr-2" type="submit">Next</loading-button>
            </div>
          </div>
      
    </div>
  </form>
  </div>
</template>

<script>
import Icon from '@app/Shared/Icon'
import Layout from '@app/Shared/Layout'
import Chart from '@app/Shared/Chart'
import TextInput from '@app/Shared/TextInput'
import NumberInput from '@app/Shared/NumberInput'
import ActionBack from '@app/Shared/ActionBack'
import TextareaInput from '@app/Shared/TextareaInput'
import LoadingButton from '@app/Shared/LoadingButton'
import SelectInput from '@app/Shared/SelectInput.vue'
import AltsList from '@app/Pages/Project/AltsList.vue'
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import ASTM from './Astm.vue'
import { uuid } from 'vue-uuid'
import { AgGridVue } from 'ag-grid-vue'
import swal from 'sweetalert'
import InlineTextEditor from '@app/Shared/InlineTextEditor'
import { EventBus } from '@app/event-bus'
export default {
  metaInfo: { title: 'Exposure' },
  components: {
    swal,
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
    AltsList,
    Chart,
    ASTM,
    AgGridVue,
    InlineTextEditor,
  },
  layout: Layout,
  remember: 'form',
  props: {
    projectData: Object,
    locationOptions: Object,
    baseUnits: String,
    concUnits: String,
    centimeterMetric: String,
    usUnits: String,
    siUnits: String,
    temperatureEntries: [Object, Array],
    helping_tips: Object,
  },
  computed: {},
  mounted() {
    this.getRedefinedUnits(this.baseUnits)
    this.getSublocations(this.projectData.project.projectData.exposureLocale.location, 'initialCall')
    EventBus.$on("navigateApplicationTo", (data) => {
      try{
        if(Array.isArray(data)){
          var urlrout=this.route(data[0],data[1])
      }else{
        var urlrout=data
      }
      this.form.intendedUrl = data;
      if(data)
        // EventBus.$off("navigateApplicationTo");
      this.store()
      }
      catch(e){
       console.log(e); 
      }
    });
  },
  created() {},
  data() {
    return {
      rowStyle: { background: 'white', fontWeight: 500 },
      calendarMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      yearsLabels: [0, 25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300],
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      length_unit: null,
      temperature_unit: null,
      sublocationOptions: null,
      exposureOptions: null,
      renderGraphKey: null,
      uuid1: uuid.v1(),
      uuid2: uuid.v1(),
      form: this.$inertia.form({
        intendedUrl: 'concrete-mixtures',
        setManually: this.projectData.project.projectData.exposureConditions.setManually,
        location: this.projectData.project.projectData.exposureLocale.location,
        subLocation: this.projectData.project.projectData.exposureLocale.subLocation,
        exposureType: this.projectData.project.projectData.exposureLocale.exposure,
        jan_temp: parseFloat(this.temperatureEntries[0]).toFixed(1),
        feb_temp: parseFloat(this.temperatureEntries[1]).toFixed(1),
        mar_temp: parseFloat(this.temperatureEntries[2]).toFixed(1),
        apr_temp: parseFloat(this.temperatureEntries[3]).toFixed(1),
        may_temp: parseFloat(this.temperatureEntries[4]).toFixed(1),
        jun_temp: parseFloat(this.temperatureEntries[5]).toFixed(1),
        jul_temp: parseFloat(this.temperatureEntries[6]).toFixed(1),
        aug_temp: parseFloat(this.temperatureEntries[7]).toFixed(1),
        sep_temp: parseFloat(this.temperatureEntries[8]).toFixed(1),
        oct_temp: parseFloat(this.temperatureEntries[9]).toFixed(1),
        nov_temp: parseFloat(this.temperatureEntries[10]).toFixed(1),
        dec_temp: parseFloat(this.temperatureEntries[11]).toFixed(1),
        useC1556: this.projectData.project.projectData.exposureConditions.useC1556,
        maxSurfaceConcentration: parseFloat(this.projectData.project.projectData.exposureConditions.maxSurfaceConcentration).toFixed(3),
        timeToBuildUp: this.projectData.project.projectData.exposureConditions.timeToBuildUp,
        prevC1556SetUsed: this.projectData.project.projectData.exposureConditions.c1556SetUsed,
        c1556SetUsed: this.projectData.project.projectData.exposureConditions.c1556SetUsed,
        c1556Sets: this.projectData.project.projectData.exposureConditions.c1556Sets,
        currentSet: this.projectData.project.projectData.exposureConditions.c1556Sets[0],
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('save.exposure-data'))
      // this.$refs.form.validate().then((success) => {
      //   if (!success) {
      //     window.scrollTo(0, 0)
      //     return
      //   }

      // })
    },
    toggleASTMWindow() {
      swal({
        text: 'Enter name of set to be created',
        content: 'input',
        button: {
          text: 'Create Set',
          closeModal: true,
        },
      }).then((value) => {
        if (!value) {
          swal('Oh no!', "Set name can't be empty", 'error')
        } else {
          this.form.prevC1556SetUsed = this.form.c1556SetUsed
          this.form.c1556SetUsed = value
          this.form.c1556Sets.push({
            ambientConcentration: 0,
            baseUnits: 'SI metric',
            bulkDiffusionCoefficient: 3.48668689674473e-12,
            concentrationUnits: '% wt. conc.',
            dateCreated: '11/25/2021',
            days: 28,
            depthConcPairs: [
              {
                concPctWt: 0.1,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.2,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.3,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.4,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.5,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.6,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.7,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.8,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.9,
                depthMM: 1.0,
              },
              {
                concPctWt: 0.9,
                depthMM: 1.0,
              },
            ],
            description: '<add description>',
            includeFirstPoint: false,
            maxSurfaceConcentration: 9.755059563321685,
            name: value,
            timeToMax: 7.4,
            astmResults: null,
          })
          this.$modal.show(
            ASTM,
            { title: this.form.c1556SetUsed, c1556Sets: this.form.c1556Sets },
            { height: 'auto', width: '80%', scrollable: true },
            {
              'before-close': async (event) => {
                // alert(1);
                this.form.c1556SetUsed = localStorage.getItem('currentSet')
                let updatedSets = null
                await axios
                  .get(`/get-astm-sets`)
                  .then(function (response) {
                    const { data } = response
                    updatedSets = data.project.projectData.exposureConditions.c1556Sets
                  })
                  .catch(function (error) {
                    console.log(error)
                  })
                this.form.c1556Sets = updatedSets
                // prevC1556SetUsed
                let setData = this.form.c1556Sets.find((set) => set.name === this.form.c1556SetUsed)
                if (setData) {
                  this.form.prevC1556SetUsed=this.form.c1556SetUsed
                }else{
                  this.form.c1556SetUsed=this.form.prevC1556SetUsed
                }
                this.updateMaxCSAndTimeToMaxBasedOnASTM(this.form.c1556SetUsed)
              },
            },
          )
        }
      })
    },
    editASTMWindow() {
      this.$modal.show(
        ASTM,
        { title: this.form.c1556SetUsed, c1556Sets: this.form.c1556Sets },
        { height: 'auto', width: '80%', scrollable: true },
        {
          'before-close': async (event) => {
            this.form.c1556SetUsed = localStorage.getItem('currentSet')
            let updatedSets = null
            await axios
              .get(`/get-astm-sets`)
              .then(function (response) {
                const { data } = response
                updatedSets = data.project.projectData.exposureConditions.c1556Sets
              })
              .catch(function (error) {
                console.log(error)
              })
            this.form.c1556Sets = updatedSets
            this.updateMaxCSAndTimeToMaxBasedOnASTM(this.form.c1556SetUsed)
          },
        },
      )
    },
    deleteASTMSet() {
      let setName = this.form.c1556SetUsed
      let keyToBeDeleted = -1
      let allSets = this.form.c1556Sets
      Object.keys(allSets).forEach(function (key) {
        if (allSets[key].name === setName) {
          keyToBeDeleted = key
        }
      })
      if (keyToBeDeleted > 0) {
        this.form.c1556Sets.splice(keyToBeDeleted, 1)
        this.form.c1556SetUsed = this.form.c1556Sets[0].name
      } else {
        swal('Oh no!', "Can't delete default set", 'error')
        return false
      }
    },
    toggleManualStatus(event) {
      // this.form.setManually = event.target.value
      console.log(this.form.setManually)
    },
    getRedefinedUnits(baseUnits) {
      axios.get(`/change-units-string/${baseUnits}`).then((response) => {
        const { data } = response
        this.area_unit = data.area_unit
        this.volume_unit = data.volume_unit
        this.weight_unit = data.weight_unit
        this.capacity_unit = data.capacity_unit
        this.length_unit = data.length_unit
        this.temperature_unit = data.temperature_unit
      })
    },
    getSublocations(value, typeOfCall = null) {
      axios.get(`/get-sublocations/${value}`).then((response) => {
        const { data } = response
        this.sublocationOptions = data.sublocations
        if (typeOfCall == null) {
          this.form.subLocation = data.default_value
        }
        this.getExposures(typeOfCall)
      })
    },
    getExposures(typeOfCall = null) {
      axios.get(`/get-exposures/${this.form.subLocation}`).then((response) => {
        const { data } = response
        this.exposureOptions = data.exposures
        if (Object.values(this.exposureOptions).indexOf(this.form.exposureType) < 0) {
          this.form.exposureType = data.default_value
        } else {
          this.form.exposureType = this.projectData.project.projectData.exposureLocale.exposure
        }
        this.getConcAndTemperatures()
        this.uuid2 = uuid.v1()
      })
    },
    getConcAndTemperatures() {
      axios
        .post(`/get-sublocation-maxconc-and-temperatures`, {
          sublocation: this.form.subLocation,
          exposure: this.form.exposureType,
          baseUnits: this.baseUnits,
          concUnits: this.concUnits,
        })
        .then((response) => {
          const { data } = response

          if (!this.form.setManually) {
            this.convertConcentrationBasedOnConcUnitAndBaseUnit(data.max_cs)
            this.form.timeToBuildUp = parseFloat(data.time_to_max).toFixed(1)
            this.form.jan_temp = parseFloat(data.jan_temp).toFixed(1)
            this.form.feb_temp = parseFloat(data.feb_temp).toFixed(1)
            this.form.mar_temp = parseFloat(data.mar_temp).toFixed(1)
            this.form.apr_temp = parseFloat(data.apr_temp).toFixed(1)
            this.form.may_temp = parseFloat(data.may_temp).toFixed(1)
            this.form.jun_temp = parseFloat(data.jun_temp).toFixed(1)
            this.form.jul_temp = parseFloat(data.jul_temp).toFixed(1)
            this.form.aug_temp = parseFloat(data.aug_temp).toFixed(1)
            this.form.sep_temp = parseFloat(data.sep_temp).toFixed(1)
            this.form.oct_temp = parseFloat(data.oct_temp).toFixed(1)
            this.form.nov_temp = parseFloat(data.nov_temp).toFixed(1)
            this.form.dec_temp = parseFloat(data.dec_temp).toFixed(1)
          }
        })
    },
    updateMaxCSAndTimeToMaxBasedOnASTM(currentSet) {
      let setData = this.form.c1556Sets.find((set) => set.name === currentSet)
      if (setData) {
        this.convertConcentrationBasedOnConcUnitAndBaseUnit(setData.maxSurfaceConcentration)
        this.form.timeToBuildUp = parseFloat(setData.timeToMax).toFixed(1)
      }
      this.uuid1 = uuid.v1()
    },
    async convertConcentrationBasedOnConcUnitAndBaseUnit(concentration) {
      let conc = null
      await axios
        .post(`/convert-conc-by-unit`, {
          concentration: concentration,
          baseUnits: this.baseUnits,
          concUnits: this.concUnits,
        })
        .then((response) => {
          const { data } = response
          conc = parseFloat(data.concentration).toFixed(3)
        })
      this.form.maxSurfaceConcentration = conc
      this.uuid1 = uuid.v1()
      this.uuid2 = uuid.v1()
    },
    createGraph1X() {
      let xAxis = []
      for (let i = 0; i < this.yearsLabels.length; i++) {
        this.yearsLabels[i] = parseFloat(i * this.form.timeToBuildUp).toFixed(0)
        xAxis.push(`${this.yearsLabels[i]}`)
      }
      return xAxis
    },
    createGraph1Y() {
      let yAxis = []
      let data = [0]
      for (let i = 0; i < this.yearsLabels.length; i++) {
        if (i > 0) {
          data.push(this.form.maxSurfaceConcentration)
        }
      }
      yAxis.push({
        fill: false,
        borderColor: '#000000',
        pointBackgroundColor: '#000000',
        borderWidth: 1.5,
        pointBorderColor: '#000000',
        pointRadius: 0,
        pointHoverBackgroundColor: '#000000',
        pointHoverBorderColor: '#000000',
        data: data,
      })
      return yAxis
    },
    createGraph2X() {
      let xAxis = []
      for (let i = 0; i < this.calendarMonths.length; i++) {
        xAxis.push(`${this.calendarMonths[i]}`)
      }
      return xAxis
    },
    createGraph2Y() {
      let yAxis = []
      let data = [this.form.jan_temp, this.form.feb_temp, this.form.mar_temp, this.form.apr_temp, this.form.may_temp, this.form.jun_temp, this.form.jul_temp, this.form.aug_temp, this.form.sep_temp, this.form.oct_temp, this.form.nov_temp, this.form.dec_temp]
      yAxis.push({
        fill: false,
        borderColor: '#000000',
        borderWidth: 1.5,
        pointBackgroundColor: '#000000',
        pointBorderColor: '#000000',
        pointRadius: 0,
        pointHoverBackgroundColor: '#000000',
        pointHoverBorderColor: '#000000',
        data: data,
      })
      return yAxis
    },
    renderGraph1(value) {
      if (value < 0 || value > 1000) {
        return false
      }
      this.uuid1 = uuid.v1()
    },
    renderGraph2() {
      this.uuid2 = uuid.v1()
    },
    renderBothGraphs() {
      this.getConcAndTemperatures()
    },
  },
}
</script>

<style scoped>
.mx-input {
  height: 40px !important;
}
.span-btn {
  cursor: pointer;
}
#graphCanvas2 {
  align-content: center;
}

#graphCanvas1 {
  align-content: center;
}
.chart1 {
  background-color: #f5f5f5;
  padding: 10px;
}

.chart2 {
  background-color: #f5f5f5;
  padding: 10px;
}

.astm-buttons {
  cursor: pointer;
}

.astm-buttons:disabled {
  pointer-events: none;
  opacity: 0.75;
}

button:disabled {
  pointer-events: none;
  opacity: 0.75;
}
.swal-content {
  margin-left: 30px;
  margin-right: 30px;
}
</style>