<template>
  <div id="locations">
    <ValidationObserver ref="form">
    <form @submit.prevent="store">
    <div class="flex w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 py-1 px-2.5">
      <h1 class="font-semibold text-2xl">Project Details</h1>
      <div class="flex">
              <!-- <inertia-link :href="route('defaults')" class="btn-indigo-flat mr-2"> Back </inertia-link> -->
              <button v-if="!$page.props.currentAnalysis" class="btn-indigo-flat mr-2"  @click="back()">Back</button>
              <loading-button id="save-button" :loading="form.processing" class="btn-indigo-flat mr-2" type="submit">Next</loading-button>
            </div>
    </div>
    <div class="bg-white overflow-hidden">
      
          <input type="hidden" id="navigateApplicationTo" name="navigateApplicationTo" class="form-control" v-model="form.intendedUrl" />
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0">Identify Project</h3>
            
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-4 gap-4 p-2.5">
            <text-input v-model="form.title" :tool-tip-text="helping_tips.title_project_details" :rules="`required|max:50`" :required="true" :error="form.errors.title" class="block" label="Title" />
            <text-input v-model="form.analyst" :tool-tip-text="helping_tips.analyst_project_details" :rules="`required|max:50`" :error="form.errors.analyst" :required="true" class="block" label="Analyst" />
            <text-input v-model="form.description" :tool-tip-text="helping_tips.description_project_details" :rules="`required|max:100`" :error="form.errors.description" :required="true" class="block" label="Description" />
            <div class="block">
              <label class="form-label text-sm font-semibold" :for="`${form.date}`">Date<span class="text-red-500 text-xs italic">*</span></label>
              <datepicker v-model="form.date" :tool-tip-text="helping_tips.date_project_details" :format="`MM/dd/yyyy`" class="w-full form-input focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent" name="uniquename" :required="true" />
            </div>
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Select Structure Type & Dimensions</h3>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 md:grid-cols-2 gap-4 p-2.5">
            <div class="leftBlockForm">
              <select-input id="structure" v-model="form.structure" :tool-tip-text="helping_tips.type_of_structure_project_details" :rules="`required`" :error="form.errors.structure" class="pb-2 w-full" :required="true" :label="'Type Of Structure'" @input="preparePlayground()">
                <option v-for="(structure, index) in defaultStructures" :key="index" :value="index">{{ structure }}</option>
              </select-input>
              <number-input v-model="form.trueThickness" :tool-tip-text="helping_tips.thickness_project_details" :min="parameterOneLowerLimit" :max="parameterOneUpperLimit" :rules="`required|min_value:${parameterOneLowerLimit}|max_value:${parameterOneUpperLimit}`" :title="`Allowable range of values is [${parameterOneLowerLimit} , ${parameterOneUpperLimit}]`" step="any" :error="form.errors.trueThickness" :required="true" class="pb-2 w-full" :label="form.structure == 'slabs and walls (1-D)' ? 'Thickness (' + length_unit + ')' : 'Width (' + length_unit + ')'" @blur="validateAndUpdateParams('trueThickness', 'slabs and walls (1-D)' ? 'Thickness (' + length_unit + ')' : 'Width (' + length_unit + ')', $event, parameterOneLowerLimit, parameterOneUpperLimit)" />
              <number-input v-model="form.clearCover" :tool-tip-text="helping_tips.reinf_depth_project_details" :min="parameterTwoLowerLimit" :max="parameterTwoUpperLimit" :rules="`required|min_value:${parameterTwoLowerLimit}|max_value:${parameterTwoUpperLimit}`" :title="`Allowable range of values is [${parameterTwoLowerLimit} , ${parameterTwoUpperLimit}]`" step="any" :error="form.errors.clearCover" :required="true" class="pb-2 w-full" :label="'Reinf. Depth (' + length_unit + ')'" @blur="validateAndUpdateParams('clearCover', 'Reinf. Depth (' + length_unit + ')', $event, parameterTwoLowerLimit, parameterTwoUpperLimit)" />
              <number-input v-model="form.structureAreaOrLength" :tool-tip-text="helping_tips.area_project_details" :min="1" :max="10000000" :rules="`required|min_value:1|max_value:10000000`" :title="`Allowable range of values is [1 , 10000000]`" step="any" :error="form.errors.structureAreaOrLength" :required="true" class="pb-2 w-full" :label="form.structure == 'slabs and walls (1-D)' ? 'Area (' + area_unit + ')' : 'Total Length (' + standard_length_unit + ')'" @blur="validateAndUpdateParams('structureAreaOrLength', 'Area or Length', $event, 1, 10000000)" />
              <span class="pb-2 w-full" :v-model="form.structureVolume">Volume Of Concrete : {{ form.structureVolume + ' ' + volume_unit }}</span>
              <br />
              <span class="pb-2 w-full"
                >Chloride concentration units : <strong>{{ $page.props.concUnits }}</strong></span
              >
            </div>
            <div id="figureCanvas" class="rightGrphBlc w-70" style="font-family: 'Segoe UI Symbol';">
             <KonvaSLXSectionPlot 
                :plotType="form.structure"
                :projectData="projectData.project.projectData"
                :key="keyKonvaSLXSectionPlot"
                :units="this.length_unit"
                :concUnits="concUnits"
                :maxConc="0"
                :allPoints="null"
                :thickness="parseFloat(form.trueThickness)"
                :clearCover="parseFloat(form.clearCover)"
                :iCurrentlyDisplayedMonth="0"
                :justSection="true"
            />
            </div>
          </div>

          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Define Economic Parameters</h3>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-4 gap-4 p-2.5">
            <number-input v-model="form.baseYear" :tool-tip-text="helping_tips.base_year_project_details" :min="2020" :max="`${new Date().getFullYear() + 5}`" :rules="`required|min_value:2020|max_value:${new Date().getFullYear() + 5}`" :title="`Allowable range of values is [2020 , ${new Date().getFullYear() + 5}]`" step="1" :error="form.errors.baseYear" :required="true" class="block" label="Base Year (e.g. 2023)" @blur="validateAndUpdateParams('baseYear', 'Base Year', $event, 2020, new Date().getFullYear() + 5)" />
            <number-input v-model="form.studyPeriod" :tool-tip-text="helping_tips.analysis_period_project_details" :min="1" :max="500" :rules="`required|min_value:1|max_value:500`" :title="`Allowable range of values is [1 , 500]`" step="1" :error="form.errors.studyPeriod" :required="true" class="block" label="Analysis Period (yrs)" @blur="validateAndUpdateParams('studyPeriod', 'Study Period', $event, 1, 500)" />
            <number-input v-model="form.inflation" :tool-tip-text="helping_tips.inflation_rate_project_details" :min="0" :max="20" :rules="`required|min_value:0|max_value:20`" :title="`Allowable range of values is [0% , 20%]`" step="any" :error="form.errors.inflation" :required="true" class="block" label="Inflation Rate (%)" @blur="validateAndUpdateParams('inflation', 'Inflation', $event, 0, 20)" />
            <number-input v-model="form.realDiscountRate" :tool-tip-text="helping_tips.discount_rate_project_details" :min="0" :max="20" :rules="`required|min_value:0|max_value:20`" :title="`Allowable range of values is [0% , 20%]`" step="any" :error="form.errors.realDiscountRate" :required="true" class="block" label="Real Discount Rate (%)" @blur="validateAndUpdateParams('realDiscountRate', 'Real Discount Rate', $event, 0, 20)" />
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Define Alternatives (Up to 6 - click to edit)</h3>
          </div>
          <div class="w-full flex flex-wrap items-center">
            <div class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-2 px-6">Name</th>
                      <th class="py-2 px-6">Description</th>
                      <th class="py-2 px-6">Action</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in form.alternatives" :key="index" :style="`color: ${colors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-2 px-6">
                        <inline-text-editor :required="true" :existing-values="getExistingAlternatives(index)" :value.sync="set.alternative.name" class="text-sm font-medium">{{ set.alternative.name }}</inline-text-editor>
                      </td>
                      <td class="py-2 px-6">
                        <inline-text-editor :max-length="50" :value.sync="set.alternative.description" class="text-sm font-medium">{{ set.alternative.description }}</inline-text-editor>
                      </td>
                      <td class="py-2 px-6">
                        <span v-if="index != 0" class="btn-indigo-small ml-auto astm-buttons" @click="deleteAlternative(index)">Delete</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <span class="pr-5 pb-3 m-2 w-full text-center">
              <span v-if="form.alternatives.length < 6" class="btn-indigo-small ml-auto astm-buttons" @click="addAlternative()">Add New</span>
            </span>
          </div>
          <div class="flex w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0"></h3>
            <div class="flex">
              <!-- <inertia-link :href="route('defaults')" class="btn-indigo-flat mr-2"> Back </inertia-link> -->
              <button class="btn-indigo-flat mr-2"  @click="back()">Back</button>
              <loading-button id="save-button" :loading="form.processing" class="btn-indigo-flat mr-2" type="submit">Next</loading-button>
            </div>
          </div>
        
    </div>
    </form>
    </ValidationObserver>
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
import AltsList from '@app/Pages/Project/AltsList.vue'
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import Datepicker from 'vuejs-datepicker'
import InlineTextEditor from '@app/Shared/InlineTextEditor'
import 'v-tooltip/dist/v-tooltip.css'
import { EventBus } from '@app/event-bus'
import KonvaSLXSectionPlot from '@app/Shared/KonvaSLXSectionPlot'

export default {
  metaInfo: { title: 'Project Details' },
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
    AltsList,
    Datepicker,
    InlineTextEditor,
    KonvaSLXSectionPlot,
  },
  layout: Layout,
  remember: 'form',
  props: {
    structures: Object,
    colors: Array,
    projectData: Object,
    baseUnits: String,
    concUnits: String,
    centimeterMetric: String,
    usUnits: String,
    siUnits: String,
    acceptableValues: Object,
    helping_tips: Object,
  },
  data() {
    return {
     
      userAgent: this.$page.props.isMobile,
      parameterOneLowerLimit: null,
      parameterOneUpperLimit: null,
      parameterTwoUpperLimit: null,
      parameterTwoLowerLimit: null,
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      length_unit: null,
      standard_length_unit: null,
      defaultStructures: this.structures,
      keyKonvaSLXSectionPlot: null,
      alternativesListTableHeaders: [
        {
          field: 'name',
          valueSetter: (params) => {
            params.data.name = params.newValue
            return true
          },
          headerName: 'Name',
          maxWidth: 300,
          editable: true,
        },
        {
          field: 'description',
          valueSetter: (params) => {
            params.data.description = params.newValue
            return true
          },
          headerName: 'Description',
          editable: true,
        },
      ],
      defaultColDef: {
        flex: 1,
      },
      form: this.$inertia.form({
        intendedUrl: 'set-exposure',
        structure: this.projectData.project.projectData.typeOfStructure,
        title: this.projectData.project.projectData.title,
        analyst: this.projectData.project.projectData.doneByName,
        description: this.projectData.project.projectData.description,
        date: this.projectData.project.projectData.date,

        trueThickness: parseFloat(this.projectData.project.projectData.structureDimensions.trueThickness).toFixed(2),
        clearCover: parseFloat(this.projectData.project.projectData.structureDimensions.clearCover).toFixed(2),
        structureAreaOrLength: parseFloat(this.projectData.project.projectData.structureDimensions.area).toFixed(2),
        structureVolume: null,

        baseYear: this.projectData.project.projectData.baseYear,
        studyPeriod: this.projectData.project.projectData.studyPeriod,
        inflation: parseFloat(this.projectData.project.projectData.inflationRate * 100).toFixed(2),
        realDiscountRate: parseFloat(this.projectData.project.projectData.realDiscountRate * 100).toFixed(2),

        alternatives: this.projectData.project.projectData.alts.alt,
      }),
    }
  },
  computed: {},
  mounted() {
    const container = document.querySelector('#figureCanvas')
    this.getRedefinedUnits(this.baseUnits)
    this.getAllAlternatives(this.form.alternatives)
    EventBus.$on("navigateApplicationTo", (data) => {
      try{
        if(Array.isArray(data)){
          // console.log('set-projet',data[0],data[1])
          var urlrout=this.route(data[0],data[1])
      }else{
        console.log('else',data)
        var urlrout=data
      }
      this.form.intendedUrl = data;
      if(data)
        //EventBus.$off("navigateApplicationTo");
      this.store()
      }
      catch(e){
       console.log(e); 
      }
    });
  },
  created() {},
  methods: {
    back(){
      this.form.intendedUrl = 'defaults';
      this.form.post(this.route('save.project-data'), {
             
            // onSuccess: () => document.getElementById('navigateApplicationTo').value='service-life-report',
        });
    },
    store() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          window.scrollTo(0, 0)
          return
        }
        this.form.post(this.route('save.project-data'))
      })
    },
    onSelectionChanged() {
      const selectedRows = this.gridApi.getSelectedNodes()
      if (selectedRows[0].rowIndex) {
        this.selectedAlternate = selectedRows[0].rowIndex
      } else {
        this.selectedAlternate = null
      }
    },
    getAllAlternatives(alts) {
      this.alternativesListTableData = []
      for (var i = 0; i < alts.length; i++) {
        this.alternativesListTableData.push({
          name: alts[i]['alternative']['name'],
          description: alts[i]['alternative']['description'],
        })
      }
    },
    getRedefinedUnits(baseUnits) {
      axios.get(`/change-units-string/${baseUnits}`).then((response) => {
        const { data } = response
        this.area_unit = data.area_unit
        this.volume_unit = data.volume_unit
        this.weight_unit = data.weight_unit
        this.capacity_unit = data.capacity_unit
        this.length_unit = data.length_unit
        this.standard_length_unit = data.standard_length_unit
        this.preparePlayground()
      })
    },
    preparePlayground() {
      let typeOfStructure = this.form.structure
      this.computeParameterAllowableRange(typeOfStructure)
      //Checking thickness limits while preparing playground
      this.form.trueThickness = parseFloat(this.projectData.project.projectData.structureDimensions.trueThickness).toFixed(2)
      if (this.form.trueThickness < this.parameterOneLowerLimit) {
        this.form.trueThickness = parseFloat(this.parameterOneLowerLimit).toFixed(2)
      } else if (this.form.trueThickness > this.parameterOneUpperLimit) {
        this.form.trueThickness = parseFloat(this.parameterOneUpperLimit).toFixed(2)
      }
      //Checking depth limits while preparing playground
      this.form.clearCover = parseFloat(this.projectData.project.projectData.structureDimensions.clearCover).toFixed(2)
      if (this.form.clearCover < this.parameterTwoLowerLimit) {
        this.form.clearCover = parseFloat(this.parameterTwoLowerLimit).toFixed(2)
      } else if (this.form.clearCover > this.parameterTwoUpperLimit) {
        this.form.clearCover = parseFloat(this.parameterTwoUpperLimit).toFixed(2)
      }

      let trueThickness = this.form.trueThickness
      this.getDefaultStructureParameterValues()
      let baseUnits = this.$props.baseUnits
      let areaOrLength = this.form.structureAreaOrLength
      this.calculateVolume(baseUnits, typeOfStructure, trueThickness, areaOrLength)
      this.getFigure(typeOfStructure)
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
      this.form.structureVolume = parseFloat(volume).toFixed(2)
    },
    computeParameterAllowableRange(typeOfStructure) {
      if (typeOfStructure == 'slabs and walls (1-D)') {
        this.parameterOneLowerLimit = this.acceptableValues.slab_thickness_lower_limit
        this.parameterOneUpperLimit = this.acceptableValues.slab_thickness_upper_limit
        this.parameterTwoLowerLimit = this.acceptableValues.slab_depth_lower_limit
        this.parameterTwoUpperLimit = this.acceptableValues.slab_depth_upper_limit
      } else {
        this.parameterOneLowerLimit = this.acceptableValues.column_width_lower_limit
        this.parameterOneUpperLimit = this.acceptableValues.column_width_upper_limit
        this.parameterTwoLowerLimit = this.acceptableValues.column_depth_lower_limit
        this.parameterTwoUpperLimit = this.acceptableValues.column_depth_upper_limit
      }
    },
    getDefaultStructureParameterValues() {
      if (this.form.clearCover >= (this.form.trueThickness * 3) / 4) {
        this.form.clearCover = parseFloat((this.form.trueThickness * 3) / 4).toFixed(2)
      }
    },
    getExistingAlternatives(index) {
      let existingAlternatives = []
      for (let i = 0; i < this.form.alternatives.length; i++) {
        let value = this.form.alternatives[i].alternative.name.trim()
        if (index != i) {
          existingAlternatives.push(value.toLowerCase())
        }
      }
      return existingAlternatives
    },
    checkExistingAlternatives(newAlternativeName) {
      let existingAlternatives = []
      for (let i = 0; i < this.form.alternatives.length; i++) {
        let changedValue = this.form.alternatives[i].alternative.name.trim()
        existingAlternatives.push(changedValue.toLowerCase())
      }
      if (existingAlternatives.indexOf(newAlternativeName.toLowerCase()) !== -1) {
        swal('Oh no!', "Alternative name can't be same", 'error')
        return false
      }
      return true
    },
    addAlternative() {
      swal({
        text: 'Enter name of alternative to be created',
        content: 'input',
        button: {
          text: 'Create',
          closeModal: true,
        },
      }).then((value) => {
        let trimmedValue = value.replace(/\s+/g, ' ').trim()
        if (!value) {
          swal('Oh no!', "Alternative name can't be empty", 'error')
        } else if (this.checkExistingAlternatives(trimmedValue)) {
          this.form.alternatives.push({
            alternative: {
              concreteMixDesign: {
                alpha: 0.0,
                beta: 0.0,
                ct: 0.05,
                d28: 8.871560120379617e-12,
                detailedBarrier: {
                  useDefault: true,
                  barrierName: '<none>',
                  areaCost: 0.0,
                  initialEfficiency: 0,
                  ageAtFailure: 0,
                  reapplyTime: 0,
                },
                dRef: 8.871560120379617e-12,
                hydration: 25.0,
                inhibitor: '<none>',
                initiationInYears: 9.416666666666666,
                isUserDefineable: false,
                m: 0.2,
                percentClassFFlyAsh: 0.0,
                percentSilicaFume: 0.0,
                percentSlag: 0.0,
                propagationInYears: 6.0,
                rebarType: {
                  type: 'Black Steel',
                  percentOfTotal: 0.012,
                  yearsFromInitToFailure: 6,
                },
                serviceLifeInYears: 15.416666666666666,
                valuesHaveChanged: false,
                waterCementRatio: 0.42,
              },
              costs: {
                0: {
                  costNumber: 0,
                  costType: 'indep.',
                  costPrior: '0',
                  costName: 'Construction cost',
                  quantity: 2000.0,
                  unitOfMeasure: 'cub. met.',
                  unitCost: 193.24,
                  totalCost: 386480.0,
                  costTiming: {
                    startDate: '0',
                    endDate: '0',
                    frequency: '0',
                  },
                },
                1: {
                  costNumber: 1,
                  costType: 'dep.',
                  costPrior: '0',
                  costName: 'Repair cost',
                  quantity: 1000.0,
                  unitOfMeasure: 'sq. m.',
                  unitCost: 400.0,
                  totalCost: 400000.0,
                  costTiming: {
                    startDate: '99',
                    endDate: '150',
                    frequency: '10',
                  },
                },
              },
              description: 'A project that uses a new mix of concrete',
              name: value,
              userDefinedMixCost: false,
              userMixCost: 0.0,
            },
          })
        }
      })
    },
    deleteAlternative(index) {
      this.form.alternatives.splice(index, 1)
    },
    validateAndUpdateParams(parameter, field, entry, min, max) {
      
      if (entry && entry > max) {
        swal('Allowable range of values for ' + field + ' is [' + min + ', ' + max + ']')
        entry = max
      } else if (entry && entry < min) {
        swal('Allowable range of values for ' + field + ' is [' + min + ', ' + max + ']')
        entry = min
      }
      if (parameter == 'baseYear' || parameter == 'studyPeriod') {
        this.form[parameter] = entry
      } else {
        this.form[parameter] = parseFloat(entry).toFixed(2)
      }

      let typeOfStructure = this.form.structure
      let trueThickness = this.form.trueThickness
      this.getDefaultStructureParameterValues()

      let baseUnits = this.$props.baseUnits
      let areaOrLength = this.form.structureAreaOrLength

      this.calculateVolume(baseUnits, typeOfStructure, trueThickness, areaOrLength)
      this.getFigure(typeOfStructure)
    },
    getFigure(typeOfStructure) {
      this.keyKonvaSLXSectionPlot = Math.round(Math.random() * 1000);
    },
  },
}

const action = function (intendedUrl) {
  document.getElementById('navigateApplicationTo').value = intendedUrl
  document.getElementById('save-button').click()
}

EventBus.$on('navigateApplicationTo', action)
</script>

<style scoped>
.mx-input {
  height: 40px !important;
}
.span-btn {
  cursor: pointer;
}
.mx-datepicker {
  width: 100% !important;
}
.mx-input {
  height: 40px !important;
}
.astm-buttons {
  cursor: pointer;
}
.swal-content {
  margin-left: 30px;
  margin-right: 30px;
}
</style>
