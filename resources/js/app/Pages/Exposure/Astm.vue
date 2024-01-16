<template>
  <div id="astm">
    <div class="bg-white overflow-hidden">
      <ValidationObserver ref="form">
        <form @submit.prevent="store">
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5 mb-5">
            <icon name="exposures" class="h-6 w-6 fill-current mr-2" />
            <h3 class="font-semibold">ASTM C1556 Estimation of Max Surface Concentration</h3>
            <span class="btn-indigo-flat ml-auto mobile-back-btn" style="cursor: pointer" @click="$emit('close')">Use Set:
              {{ form.currentSet }}</span>
          </div>
          <div class="topnav" id="myTopnav" v-if="$page.props.isProjectScreen">
            <a class="text-sm" :href="'javascript:void(0)'" @click="toggleTab()"
              :class="astmTab == 'sets' ? 'active' : ''">Sets</a>
            <a class="text-sm" :href="'javascript:void(0)'" @click="toggleTab()"
              :class="astmTab == 'guidance' ? 'active' : ''">Some Guidance</a>
            <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
              <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
            </a>
          </div>
          <div v-if="astmTab == 'sets'" class="setTab" :key="uuid4">
            <div class="p-5 text-sm flex flex-wrap lg:w-2/5 mod-responsive">
              <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                <h3 class="font-semibold">Sets</h3>
              </div>
              <div class="text-sm flex w-full flex-wrap">
                <div class="overflow-x-auto w-full">
                  <table class="w-full whitespace-nowrap text-left">
                    <thead>
                      <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                        <th class="py-1 px-3">Name (click to edit)</th>
                        <th class="py-1 px-3 text-center">Max. Conc.</th>
                        <th class="py-1 px-3 text-right">Units</th>
                      </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                      <tr v-for="sample in form.sets" :key="sample.name" @click="setCurrentSet(sample.name)"
                        class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-1 px-3">
                          <span class="text-sm font-medium text-gray-900">
                            <inline-text-editor :value.sync="sample.name"></inline-text-editor>
                          </span>
                        </td>
                        <td class="py-1 px-3 whitespace-nowrap text-center">
                          <span class="text-sm font-medium text-gray-900">{{
                            parseFloat(sample.maxSurfaceConcentration).toFixed(3) }}</span>
                        </td>
                        <td class="py-1 px-3 whitespace-nowrap text-right">
                          <span class="text-sm font-medium text-gray-900">{{ sample.concentrationUnits }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="subnav" v-if="$page.props.isProjectScreen">
                <a class="text-sm" :href="'javascript:void(0)'" @click="toggleLeftTab()"
                  :class="astmLeftTab == 'setData' ? 'active' : ''">Set Data</a>
                <a class="text-sm" :href="'javascript:void(0)'" @click="toggleLeftTab()"
                  :class="astmLeftTab == 'parameters' ? 'active' : ''">Parameters</a>
                <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
                  <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
                </a>
              </div>
              <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                <h3 class="font-semibold">Selected Set: {{ form.currentSet }}</h3>
              </div>
              <div class="text-sm flex w-full flex-wrap" v-if="astmLeftTab == 'setData'">
                <div class="overflow-x-auto w-full">
                  <table class="w-full whitespace-nowrap text-left" :key="uuid3">
                    <thead>
                      <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                        <th class="py-1 px-3">#</th>
                        <th class="py-1 px-3 text-right">Depth (mm)</th>
                        <th class="py-1 px-3 text-right">%wt. conc.</th>
                        <th class="py-1 px-3">Action</th>
                      </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                      <tr v-for="(setData, index) in form.selectedSetData" :key="index"
                        class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-1 px-3">
                          <span class="text-sm font-medium text-gray-900">{{ setData.sample }}</span>
                        </td>
                        <td class="py-1 px-3 whitespace-nowrap text-right">
                          <span class="text-sm font-medium text-gray-900">
                            <inline-text-editor :min="0" :max="1000"
                              title="Allowable range of values is [0.000, 1000.000]" :currency-symbol="''"
                              :currency-decimal-places="3" @update="renderASTMGraph(setData.depth, 'depth', index)"
                              :required="true" :close-on-blur="true" :type="'currency'"
                              :value.sync="setData.depth"></inline-text-editor>
                          </span>
                        </td>
                        <td class="py-1 px-3 whitespace-nowrap text-right">
                          <span class="text-sm font-medium text-gray-900">
                            <inline-text-editor :min="0" :max="1.0" title="Allowable range of values is [0.000, 1.000]"
                              :currency-symbol="''" :currency-decimal-places="3"
                              @update="renderASTMGraph(setData.conc, 'conc', index)" :required="true"
                              :close-on-blur="true" :type="'currency'" :value.sync="setData.conc"></inline-text-editor>
                          </span>
                        </td>
                        <td class="py-1 px-3">
                          <span v-if="form.selectedSetData.length > 6" @click="deleteSampleEntry(index)"
                            style="cursor: pointer"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor" class="text-indigo-400" style="width: 30px">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                              </path>
                            </svg></span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <span class="mt-5 pr-5 pb-2 w-full text-center">
                  <span class="btn-indigo-small ml-auto astm-buttons" @click="addNewDataRow()">Add New</span>
                </span>
              </div>
              <div class="text-sm flex w-full flex-wrap" v-if="astmLeftTab == 'parameters'">
                <number-input :min="0" :max="60000" :rules="`required|min_value:0|max_value:60000`"
                  :title="`Allowable range of values is [0 , 60000]`" v-model="form.days" step="1"
                  :error="form.errors.days" :required="true" class="mt-5 pr-5 w-full lg:w-1/2" :label="'Days'" />
                <number-input :min="0" :max="1" :rules="`required|min_value:0|max_value:1`"
                  :title="`Allowable range of values is [0 , 1]`" v-model="form.ambientConcentration" step="any"
                  :error="form.errors.ambientConcentration" :required="true" class="mt-5 pr-5 w-full lg:w-1/2"
                  :label="'Init. Conc.'" />
                <p class="font-semibold mb-5 pr-5 w-full lg:w-1/2">Depth Units: mm</p>
                <p class="font-semibold mb-5 pr-5 w-full lg:w-1/2">Concentration Units: % wt.conc</p>
                <textarea-input v-model="form.description" :error="form.errors.description" :required="true"
                  class="pr-5 pb-2 w-full" :label="'Set Description'"></textarea-input>
              </div>
            </div>
            <div class="p-5 text-sm flex flex-wrap lg:w-3/5">
              <div class="subnav" v-if="$page.props.isProjectScreen">
                <a class="text-sm" :href="'javascript:void(0)'" @click="toggleRightTab()"
                  :class="astmRightTab == 'graphs' ? 'active' : ''">Graphs</a>
                <a class="text-sm" :href="'javascript:void(0)'" @click="toggleRightTab()"
                  :class="astmRightTab == 'calculations' ? 'active' : ''">ASTM Calculations</a>
                <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
                  <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
                </a>
              </div>
              <div class="text-sm flex w-full flex-wrap" v-if="astmRightTab == 'graphs'">
                <div id="graphCanvas" class="-mr-5 -mb-2 flex flex-wrap w-full">
                  <Chart class="chart2" :key="uuid3" type="scatter" :xLabel="'Depth (mm)'"
                    :yLabel="'Concentration (% wt.conc)'" :title="form.currentSet" :labels="createGraphX()"
                    :datasets="createGraphY()" />
                </div>
                <span class="mt-10 pr-5 pb-2 w-full text-center">
                  <loading-button :loading="form.processing" class="btn-indigo-flat ml-auto mobile-back-btn"
                    type="submit">Calculate ASTM Parameters</loading-button>
                  <button class="btn-indigo-flat ml-auto mobile-back-btn" @click="closeModal()">Close</button>
                </span>
              </div>
              <div class="text-sm flex w-full flex-wrap" v-if="astmRightTab == 'calculations'">
                <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                  <h3 class="font-semibold">Table 1: Results</h3>
                </div>
                <div class="mb-10 text-sm flex w-full flex-wrap">
                  <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left">
                      <thead>
                        <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                          <th class="py-1 px-3">C_s</th>
                          <th class="py-1 px-3 text-right">C_i</th>
                          <th class="py-1 px-3 text-right">D_a</th>
                          <th class="py-1 px-3 text-right">t</th>
                          <th class="py-1 px-3 text-right">sum</th>
                        </tr>
                      </thead>
                      <tbody class="text-gray-600 text-sm font-light">
                        <tr v-for="(data, index) in form.astmCalcDataTableOneData" :key="index"
                          class="border-b border-gray-200 hover:bg-gray-100">
                          <td class="py-1 px-3">
                            <span class="text-sm font-medium text-gray-900">{{ data.C_s }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.C_i }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.D_a }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.t }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.sum }}</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
                  <h3 class="font-semibold">Table 2: Calculations</h3>
                </div>
                <div class="mb-5 text-sm flex w-full flex-wrap">
                  <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left">
                      <thead>
                        <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                          <th class="py-1 px-3">x</th>
                          <th class="py-1 px-3 text-right">Measured</th>
                          <th class="py-1 px-3 text-right">Predicted</th>
                          <th class="py-1 px-3 text-right">Meas - Pred</th>
                          <th class="py-1 px-3 text-right">Error^2</th>
                        </tr>
                      </thead>
                      <tbody v-if="form.astmCalcDataTableTwoData" class="text-gray-600 text-sm font-light">
                        <tr v-for="(data, index) in form.astmCalcDataTableTwoData" :key="index"
                          class="border-b border-gray-200 hover:bg-gray-100">
                          <td class="py-1 px-3">
                            <span class="text-sm font-medium text-gray-900">{{ data.depth }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.value }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.predicted }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.difference }}</span>
                          </td>
                          <td class="py-1 px-3 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900">{{ data.squaredDiff }}</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <span class="mt-10 pr-5 pb-2 w-full text-center">
                  <loading-button :loading="form.processing" class="btn-indigo-flat ml-auto mobile-back-btn"
                    type="submit">Calculate ASTM Parameters</loading-button>
                </span>
              </div>
            </div>
          </div>
          <div v-if="astmTab == 'guidance'">
            <div class="p-5 text-sm flex w-full flex-wrap">
              <strong>
                <h4>ASTM Designation: C1556 11</h4>
                Standard Test Method for Determining the Apparent Chloride Diffusion Coefficient of Cementitious Mixtures
                by Bulk Diffusion
              </strong>
            </div>
            <div class="p-5 text-sm flex w-full flex-wrap">Life-365 allows users to input chloride concentration data
              taken from field samples. Life-365 does not require that the data be gathered this way, but rather that it
              simply conforms to the number and depth of samples that can be created using the ASTM process. The ASTM
              Standard Method suggests that data be gathered at the following intervals, where thie table expresses values
              in millimeters (mm). (1 inch = 25.4mm)</div>
            <div class="p-5 text-sm flex w-full flex-wrap justify-center"><strong class="text-center">Table of Suggested
                Measurement Depths, by w/cm (source: ASTM)</strong></div>
            <div class="p-5 text-sm flex w-full flex-wrap justify-center"><ag-grid-vue @grid-ready="onGridReady"
                :defaultColDef="defaultColDef" :autoSizeAllColumns="true" :headerHeight="30" :rowHeight="40"
                style="width: 70%; height: 90%" :domLayout="`autoHeight`" class="ag-theme-alpine"
                :columnDefs="guidanceTableHeaders" :rowData="guidanceTableData"> </ag-grid-vue></div>
            <div class="p-5 text-sm flex w-full flex-wrap">Life-365 does follow the ASTM C1556 procedure for estimating
              the maximum surface chloride concentration from this data. It then allows the user to use this estimate as
              the maximum chloride concentration used in the Exposure tab. The user, however, still needs to input their
              own estimate of the time in years for this concentration to be reached.</div>
            <div class="p-5 text-sm flex w-full flex-wrap">The procedure also estimates a bulk diffusion coefficient for
              the concrete. This estimate is based on Fick's Second Law-based model, NOT Life-365, and thus is not used by
              Life-365 in its service life calculations.</div>
            <div class="p-5 text-sm flex w-full flex-wrap">For more information about the ASTM C1556 technique, see the
              ASTM Publication of this standard test method, as well as the Life-365 Users Manual.</div>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </div>
</template>

<script>
import Icon from '@app/Shared/Icon'
import Chart from '@app/Shared/MixedChart'
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
import { uuid } from 'vue-uuid'
import { AgGridVue } from 'ag-grid-vue'
import NProgress from 'nprogress'
import InlineTextEditor from '@app/Shared/InlineTextEditor'
import swal from 'sweetalert'

export default {
  metaInfo: { title: 'ASTM' },
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
    AgGridVue,
    InlineTextEditor,
  },
  props: {
    c1556Sets: [Object, Array],
    title: String,
  },

  // get the data from this panel
  data() {

    return {
      astmTab: 'sets',
      astmLeftTab: 'setData',
      astmRightTab: 'graphs',
      uuid3: uuid.v1(),
      uuid4: uuid.v1(),
      defaultColDef: {
        flex: 1,
      },
      guidanceTableData: [
        { depth: 'Depth 1', header2: '0-1', header3: '0-1', header4: '0-1', header5: '0-1', header6: '0-1', header7: '0-1', header8: '0-1' },
        { depth: 'Depth 2', header2: '1-2', header3: '1-2', header4: '1-2', header5: '1-3', header6: '1-3', header7: '1-3', header8: '1-5' },
        { depth: 'Depth 3', header2: '2-3', header3: '2-3', header4: '2-3', header5: '3-5', header6: '3-5', header7: '3-6', header8: '5-10' },
        { depth: 'Depth 4', header2: '3-4', header3: '3-4', header4: '3-5', header5: '5-7', header6: '5-8', header7: '6-10', header8: '10-15' },
        { depth: 'Depth 5', header2: '4-5', header3: '4-6', header4: '5-7', header5: '7-10', header6: '8-12', header7: '10-15', header8: '15-20' },
        { depth: 'Depth 6', header2: '5-6', header3: '6-8', header4: '7-9', header5: '10-13', header6: '12-16', header7: '15-20', header8: '20-25' },
        { depth: 'Depth 7', header2: '6-8', header3: '8-10', header4: '9-12', header5: '13-16', header6: '16-20', header7: '20-25', header8: '25-30' },
        { depth: 'Depth 8', header2: '8-10', header3: '10-12', header4: '12-16', header5: '16-20', header6: '20-25', header7: '25-30', header8: '30-35' },
      ],
      guidanceTableHeaders: [
        { field: 'depth', headerName: 'w/cm', cellStyle: { border: 'solid 1px' } },
        { field: 'header2', headerName: '0.25', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header3', headerName: '0.30', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header4', headerName: '0.35', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header5', headerName: '0.40', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header6', headerName: '0.50', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header7', headerName: '0.60', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
        { field: 'header8', headerName: '0.70', type: 'rightAligned', cellStyle: { border: 'solid 1px' } },
      ],
      allSamples: [],
      form: this.$inertia.form({
        sets: this.c1556Sets,
        currentSet: this.title,
        maxSurfaceConcentration: null,
        days: null,
        sumErrors: 0,
        ambientConcentration: null,
        description: null,
        bulkDiffusionCoefficient: null,
        selectedSetData: null,
        astmCalcDataTableOneData: [],
        astmCalcDataTableTwoData: [],
      }),
    }
  },
  beforeMount() {

    this.getAllSamples()
    this.getSampleData(this.form.currentSet)
    this.createGraphX()
    this.getASTMCalculationTableOneData(this.form.currentSet)
    this.getASTMCalculationTableTwoData(this.form.currentSet)
  },
  mounted() {
    this.store()
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    store() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          window.scrollTo(0, 0)
          return
        }
        this.form.processing = true
        this.assignDataToDefaultSet(this.form.currentSet)
      })
    },
    deleteSampleEntry(index) {
      swal({
        title: 'Are you sure?',
        text: 'This action cannot be undone',
        icon: 'warning',
        buttons: ['Cancel', 'Delete'],
        dangerMode: true,
      }).then((confirmed) => {
        if (confirmed) {
          this.form.selectedSetData.splice(index, 1);
          this.updateSampleData(this.form.currentSet);
          this.uuid3 = uuid.v1();
          swal('Deleted!', 'Your sample entry has been deleted.', 'success');
        }
      });
    },
    assignDataToDefaultSet: async function (setName) {
      let updateData = null
      for (let i = 0; i < this.form.sets.length; i++) {
        if (this.form.sets[i].depthConcPairs.length < 6) {
          swal('Number of elements is less than 6', 'error')
          this.form.processing = false

        }
        for (let j = 0; j < this.form.sets[i].depthConcPairs.length; j++) {

          if (this.form.sets[i].depthConcPairs[j].concPctWt < 0 || this.form.sets[i].depthConcPairs[j].concPctWt > 1) {
            swal('%wt.conc values must be greater than 0 and equal to 1', 'error')
            this.form.processing = false

          }
        }
      }
      //console.log(this.form.sets)
      await axios
        .post('/calculate-astm', {
          sets: this.form.sets,
        })
        .then(function (response) {
          const { data } = response
          //console.log(data.inputJSON.project.projectData.exposureConditions,'tere')
          updateData = data.inputJSON.project.projectData.exposureConditions
        })
        .catch(function (error) {
          const self = this; // Store the reference to 'this'

          // Now use 'self' inside the catch block
          // console.log(error.message)
          // swal(error.message, 'error')
          swal('The entered wt.conv% values produced an error. Please adjust values', 'error');
          self.form.processing = false;


        })
      //console.log(updateData.c1556Sets)
      if (updateData) {
        this.form.sets = updateData.c1556Sets
      }
      this.setCurrentSet(setName)
      this.getASTMCalculationTableOneData(setName)
      this.getASTMCalculationTableTwoData(setName)
      this.form.processing = false
    },
    setCurrentSet(setName) {
      this.form.currentSet = setName
      this.getSampleData(setName)
      this.getASTMCalculationTableOneData(setName)
      this.getASTMCalculationTableTwoData(setName)
      localStorage.setItem('currentSet', setName)
    },
    toggleTab() {
      if (this.astmTab == 'sets') {
        this.astmTab = 'guidance'
      } else {
        this.astmTab = 'sets'
      }
    },
    toggleLeftTab() {
      if (this.astmLeftTab == 'setData') {
        this.astmLeftTab = 'parameters'
      } else {
        this.astmLeftTab = 'setData'
      }
    },
    toggleRightTab() {
      if (this.astmRightTab == 'graphs') {
        this.astmRightTab = 'calculations'
      } else {
        this.astmRightTab = 'graphs'
      }
    },

    getAllSamples() {
      this.allSamples = []
      for (var i = 0; i < this.form.sets.length; i++) {
        this.allSamples.push({
          name: this.form.sets[i]['name'],
          maxcs: parseFloat(this.form.sets[i]['maxSurfaceConcentration']).toFixed(3),
          units: this.form.sets[i]['concentrationUnits'],
        })
      }
      localStorage.setItem('currentSet', this.form.currentSet)
    },
    getSampleData(currentSet) {
      const setData = this.form.sets.find((set) => set.name === currentSet)
      if (setData) {

        this.form.ambientConcentration = parseFloat(setData.ambientConcentration).toFixed(3)
        this.form.days = setData.days
        this.form.description = setData.description
        this.form.selectedSetData = []
        for (var i = 0; i < setData.depthConcPairs.length; i++) {
          this.form.selectedSetData.push({
            sample: i + 1,
            depth: setData.depthConcPairs[i]['depthMM'],
            conc: setData.depthConcPairs[i]['concPctWt'],
          })
        }
        //console.log(this.form.selectedSetData,'data')
      }
      this.uuid4 = uuid.v1()
      this.uuid3 = uuid.v1()
    },
    getASTMCalculationTableOneData(currentSet) {
      const setData = this.form.sets.find((set) => set.name === currentSet)
      this.form.astmCalcDataTableOneData = []
      if (setData) {
        this.form.astmCalcDataTableOneData.push({
          C_s: parseFloat(setData.maxSurfaceConcentration).toFixed(3),
          C_i: parseFloat(setData.ambientConcentration).toFixed(3),
          D_a: parseFloat(setData.bulkDiffusionCoefficient).toExponential(3),
          t: setData.days,
          sum: setData.astmResults ? parseFloat(setData.astmResults.sumErrors).toExponential(3) : null,
        })
      }
    },
    getASTMCalculationTableTwoData(currentSet) {
      try {
        const setData = this.form.sets.find((set) => set.name === this.form.currentSet)
        this.form.astmCalcDataTableTwoData = []
        if (setData.astmResults) {
          let dtmData = setData.astmResults.dtm.dataVector
          for (let i = 0; i < dtmData.length; i++) {
            this.form.astmCalcDataTableTwoData.push({
              depth: parseFloat(dtmData[i][0]).toFixed(3),
              value: parseFloat(dtmData[i][1]).toFixed(3),
              predicted: parseFloat(dtmData[i][2]).toFixed(3),
              difference: parseFloat(dtmData[i][3]).toExponential(3),
              squaredDiff: parseFloat(dtmData[i][4]).toExponential(3),
            })
          }
        }

      } catch (e) {
        console.log(e);
      }
    },
    addNewDataRow() {

      this.form.selectedSetData.push({

        sample: this.form.selectedSetData.length + 1,
        depth: parseFloat(1).toFixed(3),
        conc: parseFloat(1).toFixed(3),
      })
      this.uuid3 = uuid.v1()
    },
    renderASTMGraph(value, nodeName, index) {

      if (nodeName == 'depth') {
        this.form.selectedSetData[index].depth = value
      } else {
        this.form.selectedSetData[index].conc = value
      }
      this.updateSampleData(this.form.currentSet)
      this.uuid3 = uuid.v1()
    },
    updateSampleData(setName) {
      let prepareInputSampleSet = [];
      //console.log(this.form.selectedSetData,'update');
      for (let i = 0; i < this.form.selectedSetData.length; i++) {
        prepareInputSampleSet.push({
          concPctWt: this.form.selectedSetData[i].conc,
          depthMM: this.form.selectedSetData[i].depth,
        })
      }
      this.form.sets.find((set) => set.name === setName).depthConcPairs = prepareInputSampleSet
    },
    createGraphX() {
      //console.log(this.form.selectedSetData,'graph')
      // const setData = this.form.sets.find((set) => set.name === this.form.currentSet)
      let xAxis = []
      if (this.form.selectedSetData) {
        for (let i = 0; i < this.form.selectedSetData.length; i++) {
          xAxis.push(this.form.selectedSetData[i].depth)
        }
      }
      return xAxis
    },
    createGraphY() {
      //console.log(this.form.selectedSetData,'graphy')
      // const setData = this.form.sets.find((set) => set.name === this.form.currentSet)
      let yAxis = []
      let data1 = []
      let data2 = []
      if (this.form.selectedSetData) {
        for (let i = 0; i < this.form.selectedSetData.length; i++) {
          data1.push(this.form.selectedSetData[i].conc)
        }
      }
      for (let i = 0; i < this.form.astmCalcDataTableTwoData.length; i++) {
        data2.push(this.form.astmCalcDataTableTwoData[i].predicted)
      }
      yAxis.push(
        {
          fill: false,
          borderColor: '#000000',
          borderWidth: 1.5,
          pointBackgroundColor: '#f5f5f5',
          pointBorderColor: '#000000',
          pointRadius: 3,
          pointHoverBackgroundColor: '#000000',
          pointHoverBorderColor: '#000000',
          data: data1,
        },
        {
          type: 'line',
          fill: false,
          borderColor: '#000000',
          borderWidth: 1.5,
          pointBackgroundColor: '#f5f5f5',
          pointBorderColor: '#000000',
          pointRadius: 0,
          pointHoverBackgroundColor: '#000000',
          pointHoverBorderColor: '#000000',
          data: data2,
        },
      )
      return yAxis
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
  },
}
</script>

<style scoped>
.setTab {
  display: flex;
  align-items: baseline;
}

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

.subnav {
  overflow: hidden;
  background-color: #333;
  margin: 10px 10px 10px 0px;
}

.subnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.subnav a:hover {
  background-color: #ddd;
  color: black;
}

.subnav a.active {
  background-color: #7886d7;
  color: white;
}

.subnav .icon {
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

  .subnav a:not(:first-child) {
    display: none;
  }

  .subnav a.icon {
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

  .subnav.responsive {
    position: relative;
  }

  .subnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }

  .subnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}

.swal-content {
  margin-left: 30px;
  margin-right: 30px;
}

@media (max-width:600px) {
  .mod-responsive {
    max-width: 300px;
  }
}
</style>
