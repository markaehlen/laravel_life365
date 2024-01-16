<template>
  <div id="locations">
    <div class="flex w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-1 mb-5">
      <h3 class="font-semibold">Service Life Report Preview</h3>
      <div class="flex">
              <inertia-link :href="route('concrete-mixtures')" class="btn-indigo-flat mr-2"> Back </inertia-link>
              <button class="btn-indigo-flat mr-2" @click="generateReport()">Print Report</button>
              <!-- <loading-button id="save-button" :loading="form.processing" class="btn-indigo-flat mr-2" type="submit">Next</loading-button> -->
              <inertia-link class="btn-indigo-flat mr-2" :href="route('individual-cost')">
                <span>Next</span>
              </inertia-link>
      </div>
      
    </div>
    <div class="w-full" style="text-align: -webkit-center">
    <div class="overflow-scroll" style="text-align: -webkit-center; width:1px;height:1px">
      <vue-html2pdf class="w-full" :show-layout="true" :float-layout="false" :filename="`Service Life Report`" :manual-pagination="true" :enable-download="true" ref="html2Pdf" pdf-format="a4">
        <section slot="pdf-content">
          <div class="bg-white overflow-hidden p-2">
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mb-2">
              <h3 class="font-semibold">Service Life Report</h3>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs w-1/2">Project: {{ projectData.project.projectData.title }}</p>
              <p class="font-semibold text-xs w-1/2">Description: {{ projectData.project.projectData.description }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs w-1/2">Analyst: {{ projectData.project.projectData.doneByName }}</p>
              <p class="font-semibold text-xs w-1/2">Date: {{ projectData.project.projectData.date }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mt-2">
              <h3 class="font-semibold">Structure & Dimensions</h3>
            </div>
            <div class="flex flex-wrap w-full">
              <div id="figureCanvas" class="p-1 -mb-2 w-1/3">
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
                    :style="{ background: 'whitesmoke', height: '100%',paddingTop:'10%' }"
                />
              </div>
              <div id="graphCanvas1" class="p-1 -mb-2 w-1/3">
                <Chart class="chart1" :apRatio="1" :key="uuid1" type="line" :title="'Surface Concentration'" :xLabel="'Year'" :yLabel="$page.props.concUnits" :labels="createGraph1X()" :datasets="createGraph1Y()" />
              </div>
              <div id="graphCanvas2" class="p-1 -mb-2 w-1/3">
                <Chart class="chart2" :apRatio="1" :key="uuid2" type="line" :xLabel="'Month'" :yLabel="`Temperature (${temperature_unit})`" title="Temperature vs Calendar Month Graph" :labels="createGraph2X()" :datasets="createGraph2Y()" />
              </div>
            </div>
            <div class="flex flex-wrap mt-2 w-full pt-1">
              <div class="w-1/3">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">
                  {{ form.structure }}
                </div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Outer Dimension : {{ form.trueThickness }} {{ length_unit }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Clear Cover : {{ form.clearCover }} {{ length_unit }}</div>
              </div>
              <div class="w-1/3">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Surface Concentration</div>

                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Max Surface Concentration: {{ form.maxSurfaceConcentration }} {{ concUnits }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Time to buildup : {{ form.timeToBuildUp }} years</div>
              </div>
              <div class="w-1/3">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Monthly Temperatures</div>

                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Location : {{ form.location }}, {{ form.subLocation }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Exposure Type : {{ form.exposureType }}</div>
              </div>
            </div>

            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Concrete Mixes</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-3 px-6">Name</th>
                      <th class="py-3 px-6 text-center">User?</th>
                      <th class="py-3 px-6 text-center">w/cm</th>
                      <th class="py-3 px-6 text-center">SCMs</th>
                      <th class="py-3 px-6 text-center">Inhib.</th>
                      <th class="py-3 px-6 text-center">Barrier</th>
                      <th class="py-3 px-6 text-right">Reinf.</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in alternatives" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-3 px-6">
                        <span class="text-sm font-medium">{{ set.alternative.name }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.userDefinedMixCost ? 'Yes' : 'No' }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.waterCementRatio }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium"></span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.inhibitor }}</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.detailedBarrier.barrierName }}</span>
                      </td>
                      <td class="py-3 px-6 text-right">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.rebarType.type }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="mt-5 w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Diffusion Properties & Service Lives</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-3 px-6">Name</th>
                      <th class="py-3 px-6 text-center">D28</th>
                      <th class="py-3 px-6 text-center">m</th>
                      <th class="py-3 px-6 text-center">Ct ({{ concUnits }})</th>
                      <th class="py-3 px-6 text-center">Init.</th>
                      <th class="py-3 px-6 text-center">Prop.</th>
                      <th class="py-3 px-6 text-right">Service Life</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in alternatives" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-3 px-6">
                        <span class="text-sm font-medium">{{ set.alternative.name }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.d28).toExponential(2) }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.m }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.ct }}</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.initiationInYears).toFixed(2) }} yrs</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.propagationInYears).toFixed(2) }} yrs</span>
                      </td>
                      <td class="py-3 px-6 text-right">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.serviceLifeInYears).toFixed(2) }} yrs</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full html2pdf__page-break">
              <Chart class="chart2 m-5" :key="uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="`$`" title="Life Cycle Cost, by Alternative" :labels="createGraph2X()" :datasets="createGraph2Y()" />
            </div> -->
          </div>
          <br><br>
          <button class="btn-indigo-small ml-auto mobile-back-btn" @click="generateReport()">Print Report</button>
        </section>
      </vue-html2pdf>
      </div>
      <div>
       <section slot="pdf-content">
          <div class="bg-white overflow-hidden" style="max-width:800px;margin:0 auto;">
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mb-2">
              <h3 class="font-semibold">Service Life Report</h3>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs w-full lg:w-1/2">Project: {{ projectData.project.projectData.title }}</p>
              <p class="font-semibold text-xs w-full lg:w-1/2">Description: {{ projectData.project.projectData.description }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-left bg-gray-50 p-1">
              <p class="font-semibold text-xs w-full lg:w-1/2">Analyst: {{ projectData.project.projectData.doneByName }}</p>
              <p class="font-semibold text-xs w-full lg:w-1/2">Date: {{ projectData.project.projectData.date }}</p>
            </div>
            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1 mt-2">
              <h3 class="font-semibold">Structure & Dimensions</h3>
            </div>
            <div class="flex flex-wrap w-full mb-2">
              <div id="figureCanvas" class="p-1 -mb-2 w-full lg:w-1/3">
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
                    :style="{ background: 'whitesmoke', height: '100%',paddingTop:'10%' }"
                />
                <div class="w-full lg:hidden mb-1">
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">
                    {{ form.structure }}
                  </div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Outer Dimension : {{ form.trueThickness }} {{ length_unit }}</div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Clear Cover : {{ form.clearCover }} {{ length_unit }}</div>
                </div>
              </div>
              
              <div id="graphCanvas1" class="p-1 -mb-2 w-full lg:w-1/3">
                <Chart class="chart1" :apRatio="1" :key="uuid1" type="line" :title="'Surface Concentration'" :xLabel="'Year'" :yLabel="$page.props.concUnits" :labels="createGraph1X()" :datasets="createGraph1Y()" />
                
                <div class="w-full lg:hidden mb-1">
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Surface Concentration</div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Max Surface Concentration: {{ form.maxSurfaceConcentration }} {{ concUnits }}</div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Time to buildup : {{ form.timeToBuildUp }} years</div>
                </div>
              </div>
              <div id="graphCanvas2" class="p-1 -mb-2 w-full lg:w-1/3">
                <Chart class="chart2" :apRatio="1" :key="uuid2" type="line" :xLabel="'Month'" :yLabel="`Temperature (${temperature_unit})`" title="Temperature vs Calendar Month Graph" :labels="createGraph2X()" :datasets="createGraph2Y()" />
                
                <div class="w-full lg:hidden mb-1">
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Monthly Temperatures</div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Location : {{ form.location }}, {{ form.subLocation }}</div>
                  <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Exposure Type : {{ form.exposureType }}</div>
                </div>
              </div>
            </div>
            <div class="hidden lg:flex flex-wrap w-full">
              <div class="w-full lg:w-1/3 ">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">
                  {{ form.structure }}
                </div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Outer Dimension : {{ form.trueThickness }} {{ length_unit }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Clear Cover : {{ form.clearCover }} {{ length_unit }}</div>
              </div>
              <div class="w-full lg:w-1/3 ">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Surface Concentration</div>

                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Max Surface Concentration: {{ form.maxSurfaceConcentration }} {{ concUnits }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Time to buildup : {{ form.timeToBuildUp }} years</div>
              </div>
              <div class="w-full lg:w-1/3 ">
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Monthly Temperatures</div>

                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Location : {{ form.location }}, {{ form.subLocation }}</div>
                <div class="w-full bg-gray-50 p-1 text-xs" style="text-align: center">Exposure Type : {{ form.exposureType }}</div>
              </div>
            </div>

            <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Concrete Mixes</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-3 px-6">Name</th>
                      <th class="py-3 px-6 text-center">User?</th>
                      <th class="py-3 px-6 text-center">w/cm</th>
                      <th class="py-3 px-6 text-center">SCMs</th>
                      <th class="py-3 px-6 text-center">Inhib.</th>
                      <th class="py-3 px-6 text-center">Barrier</th>
                      <th class="py-3 px-6 text-right">Reinf.</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in alternatives" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-3 px-6">
                        <span class="text-sm font-medium">{{ set.alternative.name }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.userDefinedMixCost ? 'Yes' : 'No' }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.waterCementRatio }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium"></span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.inhibitor }}</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.detailedBarrier.barrierName }}</span>
                      </td>
                      <td class="py-3 px-6 text-right">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.rebarType.type }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="mt-5 w-full border-b flex flex-nowrap items-center bg-gray-50 p-1">
              <h3 class="font-semibold">Diffusion Properties & Service Lives</h3>
            </div>
            <div v-if="visibleGraph == 'life-cycle-cost'" class="text-sm flex w-full flex-wrap">
              <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-nowrap text-left">
                  <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                      <th class="py-3 px-6">Name</th>
                      <th class="py-3 px-6 text-center">D28</th>
                      <th class="py-3 px-6 text-center">m</th>
                      <th class="py-3 px-6 text-center">Ct ({{ concUnits }})</th>
                      <th class="py-3 px-6 text-center">Init.</th>
                      <th class="py-3 px-6 text-center">Prop.</th>
                      <th class="py-3 px-6 text-right">Service Life</th>
                    </tr>
                  </thead>
                  <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="(set, index) in alternatives" :key="index" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100">
                      <td class="py-3 px-6">
                        <span class="text-sm font-medium">{{ set.alternative.name }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.d28).toExponential(2) }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.m }}</span>
                      </td>
                      <td class="py-3 px-6 whitespace-nowrap text-center">
                        <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.ct }}</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.initiationInYears).toFixed(2) }} yrs</span>
                      </td>
                      <td class="py-3 px-6 text-center">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.propagationInYears).toFixed(2) }} yrs</span>
                      </td>
                      <td class="py-3 px-6 text-right">
                        <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.serviceLifeInYears).toFixed(2) }} yrs</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- <div class="p-1 -mr-5 -mb-2 flex flex-wrap w-full html2pdf__page-break">
              <Chart class="chart2 m-5" :key="uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="`$`" title="Life Cycle Cost, by Alternative" :labels="createGraph2X()" :datasets="createGraph2Y()" />
            </div> -->
          </div>
          <br><br>
          <button class="btn-indigo-small ml-auto mobile-back-btn" @click="generateReport()">Print Report</button>
        </section>
      </div>

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
import { AgGridVue } from 'ag-grid-vue'
import Chart from '@app/Shared/Chart'
import { uuid } from 'vue-uuid'
import VueHtml2pdf from 'vue-html2pdf'
import { EventBus } from '@app/event-bus'
import KonvaSLXSectionPlot from '@app/Shared/KonvaSLXSectionPlot'
export default {
  metaInfo: { title: 'Service Life Report' },
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
    KonvaSLXSectionPlot,
  },
  layout: Layout,
  props: {
    projectData: Object,
    baseUnits: String,
    colors: [Object, Array],
    temperatureEntries: [Object, Array],
  },
  computed: {},
  created() {},
  mounted() {
    const container = document.querySelector('#figureCanvas')
    this.configKonva.width = container.offsetWidth
    this.configKonva.height = container.offsetHeight
    this.getRedefinedUnits(this.baseUnits)
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
      configKonva: {
        width: 1000,
        height: 50,
      },
      visibleGraph: 'life-cycle-cost',
      secondaryVisibleGraph: 'constant-cost',
      defaultColDef: {
        flex: 1,
      },
      uuid1: uuid.v1(),
      uuid2: uuid.v1(),
      altColors: this.colors,
      activeAlternativeIndex: 0,
      alternatives: this.projectData.project.projectData.alts.alt,
      concUnits: this.projectData.project.projectData.concentrationUnits,

      defaultStructures: this.structures,
      //Slab Configuration
      slabConfig: null,
      slabThicknessArrow: null,
      slabThicknessText: null,
      slabDepthArrow: null,
      slabDepthText: null,
      slabCircle1: null,
      slabCircle2: null,
      slabCircle3: null,
      slabCircle4: null,
      //Square Column Configuration
      squareConfig: null,
      squareThicknessArrow: null,
      squareThicknessText: null,
      squareDepthArrow: null,
      squareDepthText: null,
      squareCircle1: null,
      squareCircle2: null,
      squareCircle3: null,
      squareCircle4: null,
      //Circular Column Configuration
      circleConfig: null,
      circleThicknessArrow: null,
      circleThicknessText: null,
      circleDepthArrow: null,
      circleDepthText: null,
      circleCircle1: null,
      circleCircle2: null,
      circleCircle3: null,
      circleCircle4: null,
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      length_unit: null,
      temperature_unit: null,

      keyKonvaSLXSectionPlot:null,

      calendarMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      yearsLabels: [0, 25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300],

      form: this.$inertia.form({
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
        c1556SetUsed: this.projectData.project.projectData.exposureConditions.c1556SetUsed,
        c1556Sets: this.projectData.project.projectData.exposureConditions.c1556Sets,
        currentSet: this.projectData.project.projectData.exposureConditions.c1556Sets[0],
      }),
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
    getRedefinedUnits(baseUnits) {
      axios.get(`/change-units-string/${baseUnits}`).then((response) => {
        const { data } = response
        this.area_unit = data.area_unit
        this.volume_unit = data.volume_unit
        this.weight_unit = data.weight_unit
        this.capacity_unit = data.capacity_unit
        this.length_unit = data.length_unit
        // this.standard_length_unit = data.standard_length_unit
        this.preparePlayground()
      })
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
    preparePlayground() {
      let typeOfStructure = this.form.structure
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
      let baseUnits = this.$props.baseUnits
      let areaOrLength = this.form.structureAreaOrLength
      this.calculateVolume(baseUnits, typeOfStructure, trueThickness, areaOrLength)
      this.getFigure(typeOfStructure)
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
    renderBothGraphs() {
      this.getConcAndTemperatures()
    },
    getFigure(typeOfStructure) {
      this.keyKonvaSLXSectionPlot = Math.round(Math.random() * 1000);
    },
  },
}
</script>

<style scoped>
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
