<template>
  <div id="locations">
    <ValidationObserver ref="form">
    <form @submit.prevent="store">
    <div class="flex w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 py-1 px-2.5">
      <h1 class="font-semibold text-2xl">Concrete Mixtures</h1>
      <div class="flex">
              <!-- <button class="btn-indigo-flat mr-2"  @click="back()">Back</button> -->
              <inertia-link :href="route('set-exposure')" class="btn-indigo-flat mr-2"> Back </inertia-link>

              <!-- <inertia-link :href="route('set-exposure')" class="btn-indigo-flat mr-2" id="back"> Back </inertia-link> -->
              <!-- <inertia-link class="btn-indigo-flat mr-2" :href="route('service-life-report')">
                <span>Next</span>
              </inertia-link> -->
              <!-- <inertia-link class="btn-indigo-flat" :href="route('individual-cost')" id="next">
                <span>Next</span>
              </inertia-link> -->
              <button class="btn-indigo-flat mr-2"  @click="next()">Next</button>
      </div>
    </div>
    <div class="bg-white overflow-hidden">
      
          
          <input type="hidden" id="navigateApplicationTo" name="navigateApplicationTo" class="form-control" v-model="form.intendedUrl" />
          <div class="py-1 px-2.5 flex flex-wrap justify-center items-center">
            <span class="font-semibold mx-2 ">Type: {{ form.structure }}</span>
            <div class="text-center w-full 2xl:w-auto xl:w-auto lg:w-auto md:w-auto py-2 2xl:py-0 xl:py-0 lg:py-0 md:py-0">
              <!-- <loading-button :loading="form.processing" id="save-button" class="mx-2 btn-indigo-small mobile-back-btn" type="submit" @click="removeRowIncludedNor()">Calculate Service Life</loading-button> -->
              <button class="mx-2 btn-indigo-small mobile-back-btn"  @click="removeRowIncludedNor()">Calculate Service Life</button>
             
            </div>
            <div class="text-center w-full 2xl:w-auto xl:w-auto lg:w-auto md:w-auto py-2 2xl:py-0 xl:py-0 lg:py-0 md:py-0">
              
              <!-- <input @input="toggleNeedToCompute(form.useUncertainty)" :checked="form.useUncertainty" class="p-2 mx-2" v-model="form.useUncertainty" type="checkbox" />
              <label class="font-semibold">Compute Uncertainty </label> -->
            </div>
            <div class="text-center w-full 2xl:w-auto xl:w-auto lg:w-auto md:w-auto py-2 2xl:py-0 xl:py-0 lg:py-0 md:py-0">
              <span v-if="form.useUncertainty" class="mx-2 btn-indigo-small mobile-back-btn" @click="openUncertaintyPopup()" style="cursor: pointer">Setting</span>
              <a :href="`/help/uncertainty-analysis`" v-if="form.useUncertainty" class="mx-2 btn-indigo-small mobile-back-btn" style="cursor: pointer">Help</a>
            </div>
            
          </div>
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0">Select Name to edit properties</h3>
            
          </div>
          <div class="text-sm flex w-full flex-wrap">
            <div class="overflow-x-auto w-full">
              <table class="w-full whitespace-nowrap text-left">
                <thead>
                  <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4 text-right">Service Life (yrs)</th>
                    <th class="py-2 px-4 text-center">User Defined</th>
                    <th class="py-2 px-4 text-center">D28 (in*in/sec)</th>
                    <th class="py-2 px-4 text-center">m</th>
                    <th class="py-2 px-4 text-center">Ct ({{ form.concUnits }})</th>
                    <th class="py-2 px-4 text-center">Init. (yrs)</th>
                    <th class="py-2 px-4 text-center">Prop. (yrs)</th>
                    
                  </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                 
                  <tr v-for="(set, index) in form.alternatives" :key="index" @click="setActiveAlternative(index)" :style="`color: ${altColors[index]}`" class="border-b border-gray-200 hover:bg-gray-100" id="compute">
                    <td class="py-2 px-4">
                      <span class="text-sm font-medium">{{ set.alternative.name }}</span>
                    </td>
                    <td class="py-2 px-4 text-right">
                      <span class="text-sm font-medium">{{parseFloat(set.alternative.concreteMixDesign.serviceLifeInYears).toFixed(2)}}</span>
                    </td>
                    <td class="py-2 px-4 whitespace-nowrap text-center">
                      <span class="text-sm font-medium">{{ set.alternative.userDefinedMixCost ? 'Yes' : 'No' }}</span>
                    </td>
                    <td class="py-2 px-4 whitespace-nowrap text-center">
                      <span class="text-sm font-medium">{{  parseFloat(set.alternative.concreteMixDesign.d28).toExponential(3) }}</span>
                    </td>
                    <td class="py-2 px-4 whitespace-nowrap text-center">
                      <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.m  }}</span>
                    </td>
                    <td class="py-2 px-4 whitespace-nowrap text-center">
                      <span class="text-sm font-medium">{{ set.alternative.concreteMixDesign.ct  }}</span>
                    </td>
                    <td class="py-2 px-4 text-center">
                      <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.initiationInYears).toFixed(2) }}</span>
                    </td>
                    <td class="py-2 px-4 text-center">
                      <span class="text-sm font-medium">{{ parseFloat(set.alternative.concreteMixDesign.propagationInYears).toFixed(2) }}</span>
                    </td>
                    
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- <ag-grid-vue @grid-ready="onGridReady" :defaultColDef="defaultColDef" :suppressSizeToFit="true" :autoSizeAllColumns="true" :headerHeight="30" :rowHeight="40" style="width: 100%; height: 90%" :domLayout="`autoHeight`" class="ag-theme-alpine" :columnDefs="allSetsHeaders" :rowData="form.allSetsData"> </ag-grid-vue> -->
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold" :style="`color: ${altColors[activeAlternativeIndex]}`"> {{ form.activeAlternativeName }} </h3>
          </div>
          <div class="grey-bg-block grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-4 p-2.5">
            <div class="widthFullBlock">
              <h5 class="pr-5 pb-2 w-full font-semibold"><u>Mixture</u></h5>
             
              <number-input :disabled="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0.25" :max="0.6" @blur="validateAndUpdateParams('waterCementRatio', 'w/cm', $event, 0.25, 0.6)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.waterCementRatio" :tool-tip-text="helping_tips.mixture_w_cm" :rules="`required|min_value:0.25|max_value:0.60`" :title="`Allowable range of values is [0.25 , 0.60]`" step="any" :error="form.errors.waterCementRatio" :required="true" class="block" :label="'w/cm'" />
              <number-input :disabled="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0.0" :max="50.00" @blur="validateAndUpdateParamsforPercentage('percentClassFFlyAsh', 'Class F fly ash', $event, 0.0, 50.0)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.percentClassFFlyAsh" :tool-tip-text="helping_tips.mixture_class" :rules="`required|min_value:0|max_value:50`" :title="`Allowable range of values is [0.00 , 50.00]`" step="any" :error="form.errors.costBlackSteel" :required="true" class="block" :label="'Class F fly ash (%)'" id="FlyAsh" />
              <number-input :disabled="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0.0" :max="70.00" @blur="validateAndUpdateParamsforPercentage('percentSlag', 'Slag', $event, 0.0, 70.0)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.percentSlag" :tool-tip-text="helping_tips.mixture_slag" :rules="`required|min_value:0|max_value:70`" :title="`Allowable range of values is [0.00 , 70.00]`" step="any" :error="form.errors.costEpoxy" :required="true" class="block" :label="'Slag (%)'" id="Slag" />
              <number-input :disabled="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0.0" :max="15.00" @blur="validateAndUpdateParamsforPercentage('percentSilicaFume', 'Silica Fume', $event, 0.0, 15.0)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.percentSilicaFume" :tool-tip-text="helping_tips.mixture_silica" :rules="`required|min_value:0|max_value:15`" :title="`Allowable range of values is [0.00 , 15.00]`" step="any" :error="form.errors.costStainless" :required="true" class="block" :label="'Silica Fume (%)'" />
            </div>
            <div class="widthFullBlock">
              <h5 class="pr-5 pb-2 w-full font-semibold"><u>Rebar</u></h5>
              <select-input :rules="`required`" @blur="valuesHaveChanged()" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.rebarType['type']" :tool-tip-text="helping_tips.mixture_rebart" :error="form.errors.structure" class="block" :required="true" :label="'Rebar Steel Type'">
                <option v-for="(steelType, index) in rebarSteelTypesOptions" :key="index" :value="steelType">{{ steelType }}</option>
              </select-input>
              <number-input :min="0" :max="30" @blur="validateAndUpdateParams('percentOfTotal', 'Rebar % vol. concrete', $event, 0, 30)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.rebarType['percentOfTotal']" :rules="`required|min_value:0|max_value:30`" :title="`Allowable range of values is [0 , 30]`" step="any" :error="form.errors.costSealer" :required="true" class="block" :label="'Rebar % vol. concrete.'" />
              <h5 class="block font-semibold"><u>Inhibitor</u></h5>
              <select-input :disabled="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" @blur="valuesHaveChanged()" :tool-tip-text="helping_tips.mixture_inhibitor" :rules="`required`" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.inhibitor" :error="form.errors.structure" class="block" :required="true" :label="'Inhibitor'">
                <option v-for="(inhibitorType, index) in inhibitorsOptions" :key="index" :value="inhibitorType">{{ inhibitorType }}</option>
              </select-input>
            </div>
            <div class="widthFullBlock">
              <h5 class="pr-5 pb-2 w-full font-semibold"><u>Barriers</u></h5>
              <select-input :rules="`required`" @blur="valuesHaveChanged()" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['barrierName']" :tool-tip-text="helping_tips.mixture_barrier" :error="form.errors.structure" class="block" :required="true" :label="'Barrier'">
                <option v-for="(barrier, index) in barrierTypesOptions" :key="index" :value="barrier">{{ barrier }}</option>
              </select-input>
              <div v-if="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['barrierName'] !== '<none>'" class="w-full border-b flex flex-nowrap items-center bg-gray-50 px-5 mr-5">
                <input @input="valuesHaveChanged()" :checked="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['useDefault']" class="p-2" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['useDefault']" type="checkbox" />
                <label class="p-4 font-semibold">Default</label>
              </div>
              <!-- form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['initialEfficiency'] -->
              <!-- form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['ageAtFailure'] -->
              
              <number-input v-if="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['barrierName'] !== '<none>'" :disabled="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['useDefault']" :min="0" :max="100" @blur="validateAndUpdateParams('repairCost', 'Repair Cost', $event, 0, 100)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['initialEfficiency']" :tool-tip-text="helping_tips.mixture_barrier" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.repairCost" :required="true" class="pr-5 pb-2 w-full" :label="'Initial efficiency (%)'" />
              <number-input v-if="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['barrierName'] !== '<none>'" :disabled="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['useDefault']" :min="0" :max="500" @blur="validateAndUpdateParams('areaToRepairPct', 'Area to repair percentage', $event, 0, 500)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['ageAtFailure']" :tool-tip-text="helping_tips.mixture_barrier" :rules="`required|min_value:0|max_value:500`" :title="`Allowable range of values is [0 , 500]`" step="any" :error="form.errors.areaToRepairPct" :required="true" class="pr-5 pb-2 w-full" label="Age at failure (yrs)" />
              <number-input v-if="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['barrierName'] !== '<none>'" :disabled="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['useDefault']" :min="0" :max="500" @blur="validateAndUpdateParams('repairIntervalYrs', 'Repair Interval', $event, 0, 500)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.detailedBarrier['reapplyTime']" :tool-tip-text="helping_tips.mixture_barrier" :rules="`required|min_value:0|max_value:500`" :title="`Allowable range of values is [0 , 500]`" step="1" :error="form.errors.repairIntervalYrs" :required="true" class="pr-5 pb-2 w-full" label="#reapplytimes" />
            </div>
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-2.5">
            <input @click="needToRegenerateGraphsAgain()" :checked="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" class="p-2" v-model="form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" type="checkbox" />
            <label class="pl-1 font-semibold">Custom </label>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-5 md:grid-cols-5 gap-4 p-2.5">
            <number-input :disabled="!form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.dRef" step="1" :tool-tip-text="helping_tips.mixture_d28" :error="form.errors.baseYear" :required="true" class="block" label="D28 (in*in/sec)" />
            <number-input :disabled="!form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0" :max="0.6" @blur="validateAndUpdateParams('m', 'm', $event, 0.0, 0.6)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.m" :tool-tip-text="helping_tips.mixture_m" :rules="`required|min_value:0|max_value:0.60`" :title="`Allowable range of values is [0 , 0.60]`" step="any" :error="form.errors.studyPeriod" :required="true" class="block" label="m" />
            <number-input :disabled="!form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0.1" :max="500" @blur="validateAndUpdateParams('hydration', 'Hydration', $event, 0.1, 500)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.hydration" :tool-tip-text="helping_tips.mixture_hydration" :rules="`required|min_value:0.1|max_value:500`" :title="`Allowable range of values is [0.1 , 500]`" step="any" :error="form.errors.hydration" :required="true" class="block" label="Hydration (yrs)" />
            <number-input :disabled="!form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0" :max="10" @blur="validateAndUpdateParams('ct', 'Ct', $event, 0, 10)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.ct" :tool-tip-text="helping_tips.mixture_ct" :rules="`required|min_value:0|max_value:10`" :title="`Allowable range of values is [0 , 10]`" step="any" :error="form.errors.discount" :required="true" class="block" :label="`Ct (${form.concUnits})`" />
            <number-input :disabled="!form.alternatives[activeAlternativeIndex].alternative.userDefinedMixCost" :min="0" :max="100" @blur="validateAndUpdateParams('propagationInYears', 'Propagation', $event, 0, 100)" v-model="form.alternatives[activeAlternativeIndex].alternative.concreteMixDesign.propagationInYears" :tool-tip-text="helping_tips.mixture_prop" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.discount" :required="true" class="block" label="Prop. (yrs)" />
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Service Life Graphs <small v-if="needToCompute || needToRegenerateGraphs">(Need to re-generate graphs)</small></h3>
          </div>
          <div class="topnav" v-if="$page.props.isProjectScreen && !needToCompute && !needToRegenerateGraphs">
            <a class="text-sm" @click="toggleGraphTab('service-life')" :class="visibleGraph == 'service-life' ? 'active' : ''">Service Life</a>
            <a class="text-sm" @click="toggleGraphTab('cross-section')" :class="visibleGraph == 'cross-section' ? 'active' : ''">Cross-Section</a>
            <a class="text-sm" @click="toggleGraphTab('initiation')" :class="visibleGraph == 'initiation' ? 'active' : ''">Initiation</a>
            <a class="text-sm" @click="toggleGraphTab('conc-characteristics')" :class="visibleGraph == 'conc-characteristics' ? 'active' : ''">Conc Characteristics</a>
            <a v-if="form.useUncertainty" class="text-sm" @click="toggleGraphTab('init-prob')" :class="visibleGraph == 'init-prob' ? 'active' : ''">Init Prob</a>
            <a v-if="form.useUncertainty" class="text-sm" @click="toggleGraphTab('init-variation')" :class="visibleGraph == 'init-variation' ? 'active' : ''">Init Variation</a>
            <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
              <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
            </a>
          </div>
          <div id="forReferenceOfConcentration" v-if="visibleGraph == 'service-life' && !needToCompute && !needToRegenerateGraphs" class="p-2.5 flex flex-wrap w-full">
            <Chart class="chart2" :stacked="true" :key="uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="`Years`" title="Service Life" :labels="createGraph2X()" :datasets="createGraph2Y()" />
          </div>
          <div v-if="visibleGraph == 'cross-section' && !needToCompute && !needToRegenerateGraphs" class="p-2.5 flex flex-wrap w-full">
            <!-- <b class="-mr-5 -mb-2 flex flex-wrap w-full">NOTE - Under Development (Radial gradients are under study)</b> -->
              
              <select-input id="graphvalue"  class="pb-2 w-full" :label="'Select'" v-model="selectedOption" @blur="handleChange()">
                <option v-for="(set, index) in form.alternatives" :key="index" :value="set.alternative.name">{{ set.alternative.name }} </option>
              </select-input>
              
            <div id="figureCanvas" class="flex flex-wrap w-full">
               
               <KonvaSLXSectionPlot 
               
                    :plotType="form.structure"
                    :projectData="projectData.project.projectData"
                    :units="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].units"
                    :concUnits="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].concentrationUnits"
                    :maxConc="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].maxConcentration"
                    :allPoints="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].allPoints"
                    :thickness="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].overallThickness"
                    :clearCover="this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].clearCover"
                    :iCurrentlyDisplayedMonth="iCurrentlyDisplayedMonth"
                    :key="iCurrentlyDisplayedMonth"
                    :justSection="false"
                />
            </div>
            <!-- <div id="figureCanvas" class="flex flex-wrap w-full">
              <KonvaSLXSectionPlot 
                v-if="selectedOptionData"
                :plotType="form.structure"
                :projectData="projectData.project.projectData"
                :units="selectedOptionData.units"
                :concUnits="selectedOptionData.concentrationUnits"
                :maxConc="selectedOptionData.maxConcentration"
                :allPoints="selectedOptionData.allPoints"
                :thickness="selectedOptionData.overallThickness"
                :clearCover="selectedOptionData.clearCover"
                :iCurrentlyDisplayedMonth="iCurrentlyDisplayedMonth"
                :key="iCurrentlyDisplayedMonth"
                :justSection="false"
              />
            </div> -->



            <div class="p-1 -mr-5 mb-2 flex w-full justify-center flex-wrap">
            Select nearest year [0 to init] 
            <vue-range-slider 
              ref="slider" 
              :min="1"
              :step="1"
              :realTime="true"
              tooltip="hover"
              v-model="iCurrentlyDisplayedMonth"
              :max="parseFloat(this.serviceLifeResults.results['Cross-Section Graph'].results[this.form.activeAlternativeName].allPoints.length)" 
            ></vue-range-slider>
            <span class="ml-2">Year = {{ parseFloat(iCurrentlyDisplayedMonth/12).toFixed(2) }}</span>
            </div>
          </div>

          <div v-if="visibleGraph == 'initiation' && !needToCompute && !needToRegenerateGraphs" class="p-1 -mr-5 -mb-2 flex w-full flex-wrap">
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`${serviceLifeGraphs.InitConcVsDepth.xTitle}`" :yLabel="`${serviceLifeGraphs.InitConcVsDepth.yTitle}`" :title="`${serviceLifeGraphs.InitConcVsDepth.title}`" :labels="InitConcVsDepthX()" :datasets="InitConcVsDepthY()" />
          </div>
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`${serviceLifeGraphs.InitConcVsDepthAtTime.xTitle}`" :yLabel="`${serviceLifeGraphs.InitConcVsDepthAtTime.yTitle}`" :title="`${serviceLifeGraphs.InitConcVsDepthAtTime.title}`" :labels="InitConcVsDepthAtTimeX()" :datasets="InitConcVsDepthAtTimeY()" />
          </div>
          </div>
          <div v-if="visibleGraph == 'conc-characteristics' && !needToCompute && !needToRegenerateGraphs" class="p-1 -mr-5 -mb-2 flex w-full flex-wrap text-center">
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`Year`" :yLabel="`${serviceLifeGraphs.ConcCharDiffusivity.yTitle}`" :title="`Diffusivity Versus Time`" :labels="ConcCharDiffusivityX()" :datasets="ConcCharDiffusivityY()" />
          </div>
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`${serviceLifeGraphs.ConcCharSurface.xTitle}`" :yLabel="`${serviceLifeGraphs.ConcCharSurface.yTitle}`" :title="`${serviceLifeGraphs.ConcCharSurface.title}`" :labels="ConcCharSurfaceX()" :datasets="ConcCharSurfaceY()" />
          </div>
          </div>
          <div v-if="visibleGraph == 'init-prob' && form.useUncertainty && !needToCompute && !needToRegenerateGraphs" class="p-1 -mr-5 -mb-2 flex w-full flex-wrap text-center">
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`${serviceLifeGraphs.InitPeriodProbByYear.xTitle}`" :yLabel="`${serviceLifeGraphs.InitPeriodProbByYear.yTitle}`" :title="`${serviceLifeGraphs.InitPeriodProbByYear.title}`" :labels="InitPeriodProbByYearX()" :datasets="InitPeriodProbByYearY()" />
          </div>
          <div class="w-full lg:w-1/2">
            <Chart class="chart2 m-5" :apRatio="$page.props.isMobile? 0.75 : 1.5" :stacked="true" :key="uuid2" type="line" :xLabel="`${serviceLifeGraphs.CumulativeProbByYear.xTitle}`" :yLabel="`${serviceLifeGraphs.CumulativeProbByYear.yTitle}`" :title="`${serviceLifeGraphs.CumulativeProbByYear.title}`" :labels="CumulativeProbByYearX()" :datasets="CumulativeProbByYearY()" />
          </div>
          </div>
          <div v-if="visibleGraph == 'init-variation' && form.useUncertainty && !needToCompute && !needToRegenerateGraphs" class="p-1 -mr-5 -mb-2 flex flex-wrap w-full">
            <Chart class="chart2" :stacked="true" :apRatio="$page.props.isMobile? 0.75 : 1.5" :key="uuid2" type="bar" :xLabel="'Alternatives'" :yLabel="``" title="Service Life" :labels="InitVariationX()" :datasets="InitVariationY()" />
          </div>
          <div class="flex w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0"></h3>
            <div class="flex">
              <!-- <inertia-link :href="route('set-exposure')" class="btn-indigo-flat mr-2"> Back </inertia-link> -->
              <button class="btn-indigo-flat mr-2"  @click="back()">Back</button>
              <!-- <inertia-link class="btn-indigo-flat mr-2" :href="route('service-life-report')">
                <span>Next</span>
              </inertia-link> -->
              <!-- <inertia-link class="btn-indigo-flat" :href="route('individual-cost')"> -->
                <button class="btn-indigo-flat mr-2"  @click="next()">Next</button>
                <!-- <span>Next</span>
              </inertia-link> -->
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
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import { AgGridVue } from 'ag-grid-vue'
import Chart from '@app/Shared/Chart'
import { uuid } from 'vue-uuid'
import 'vue-range-component/dist/vue-range-slider.css'
import VueRangeSlider from 'vue-range-component'
import UP from './UncertaintyPopup.vue'
import Info from '@app/Shared/Info'
import { EventBus } from '@app/event-bus'
import KonvaSLXSectionPlot from '@app/Shared/KonvaSLXSectionPlot'
import { Inertia } from '@inertiajs/inertia'

export default {
  metaInfo: { title: 'Concrete Mixtures' },
  components: {
    Icon,
    pickBy,
    KonvaSLXSectionPlot,
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
    UP,
    VueRangeSlider,
    Info,
  },
  layout: Layout,
  remember: 'form',
  props: {
    projectData: Object,
    serviceLifeResults: Object,
    rebarSteelTypes: Object,
    inhibitors: Object,
    barrierTypes: Object,
    colors: [Object, Array],
    helping_tips: Object,
  },
  computed: {
        
  },
  mounted() {
    for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
     
      if (this.projectData.project.projectData.alts.alt[i].alternative.concreteMixDesign.initiationInYears >= this.projectData.project.projectData.studyPeriod) {
        this.$modal.show(Info, { title: "Information", content: 'Some alternatives under analysis have greater service life than study period. In this case, there won\'t be any repair cost for those alternatives.' }, { height: 'auto', width: '30%', scrollable: true })
        break
      }
    }
    const container = document.querySelector('#forReferenceOfConcentration')    
    this.staticParameterForConcGraphs = parseFloat(this.projectData.project.projectData.structureDimensions.clearCover).toFixed(2)
    // this.preparePlayground()
     EventBus.$on("navigateApplicationTo", (data) => {
        try{
          if(Array.isArray(data)){
          // console.log('set-projet',data[0],data[1])
          var urlrout=this.route(data[0],data[1])
          }else{
            
            var urlrout=data
          }
          
          this.form.intendedUrl = data;
          if(data)
        //EventBus.$off("navigateApplicationTo");
          this.store()
          // if(data)
          //   if(Array.isArray(data)){
          //     this.$inertia.visit(this.route(data[0],data[1]), { method: 'get' })
          //   }else{
          //     this.$inertia.visit(this.route(data), { method: 'get' })
          //   }
            // EventBus.$off("navigateApplicationTo");
          }
        catch(e){
          console.log(e); 
        }
      });

  },
  created() {},
  data() {
    
    return {
     
      staticParameterForConcGraphs: null,
      serviceLifeGraphs: this.serviceLifeResults.results,
      //Slab Configuration
      userAgent: this.$page.props.isMobile,
      //All Units
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      //Select Options
      rebarSteelTypesOptions: this.rebarSteelTypes,
      inhibitorsOptions: this.inhibitors,
      
      barrierTypesOptions: this.barrierTypes,
      selectedOption: 'Base case',
      
      visibleGraph: 'service-life',
      defaultColDef: {
        flex: 1,
      },
      uuid1: uuid.v1(),
      uuid2: uuid.v1(),
      altColors: this.colors,
      activeAlternativeIndex: 0,
      needToRegenerateGraphs: false,
      needToCompute: false,
      //Form
      form: this.$inertia.form({
        intendedUrl: 'service-life-report',
        structure: this.projectData.project.projectData.typeOfStructure,
        baseUnits: this.projectData.project.projectData.baseUnits,
        concUnits: this.projectData.project.projectData.concentrationUnits,
        alternatives: this.projectData.project.projectData.alts.alt,
        activeAlternativeName: this.projectData.project.projectData.alts.alt[0].alternative.name,
        activeAlternativeDesc: this.projectData.project.projectData.alts.alt[0].alternative.description,

        useUncertainty: this.projectData.project.projectData.uncertainty.useUncertainty,
        useUncertaintyDefaults: this.projectData.project.projectData.uncertainty.useDefaults,
        coVd28: this.projectData.project.projectData.uncertainty.coVd28,
        coVm: this.projectData.project.projectData.uncertainty.coVm,
        covCs: this.projectData.project.projectData.uncertainty.covCs,
        covCt: this.projectData.project.projectData.uncertainty.covCt,
        covCover: this.projectData.project.projectData.uncertainty.covCover,
      }),
      
      iCurrentlyDisplayedMonth :this.serviceLifeResults.results['Cross-Section Graph'].results[this.projectData.project.projectData.alts.alt[0].alternative.name].iMonthOfInit,
    }
  },
  methods: {
    handleChange() {
      
      var option=document.getElementById('graphvalue').value;
      console.log(option),
      this.form.activeAlternativeName=option;
      
    },
    next(){
        this.form.intendedUrl = 'service-life-report';
        this.form.post(this.route('compute-servicelife'), {
             
            // onSuccess: () => document.getElementById('navigateApplicationTo').value='service-life-report',
        });
    },
    back(){
      this.form.intendedUrl = 'set-exposure';
      this.form.post(this.route('compute-servicelife'), {
             
            // onSuccess: () => document.getElementById('navigateApplicationTo').value='service-life-report',
            
        });
    },
    removeRowIncludedNor() {
     
      document.getElementById('navigateApplicationTo').value = 'concrete-mixtures';
            
      // this.form.intendedUrl = 'concrete-mixtures';    
       this.form.post(this.route('compute-servicelife'), {
             
      //       onSuccess: () => console.log('1'),
            
            });
            
            },
    store() {
      
      this.form.intendedUrl = document.getElementById('navigateApplicationTo').value;
      this.form.post(this.route('compute-servicelife'), {
             
            // onSuccess: () => document.getElementById('navigateApplicationTo').value='service-life-report',
               onSuccess: () =>location.reload(),
        });
    },
    openUncertaintyPopup() {
      this.$modal.show(
        UP,
        { uncertainty: this.projectData.project.projectData.uncertainty },
        { height: 'auto', width: '20%', scrollable: true },
        {
          'before-close': async (event) => {},
        },
      )
    },
    toggleNeedToCompute(currentCheck = null) {
      if (!currentCheck) {
        swal({
          title: 'Do you want to compute uncertainty?',
          text: 'In addition to computing the best-estimate service life of concrete mixes, Life365 can also compute the uncertainty in the service lives of mixes.  Computing this uncertainty in service life is not required for estimating the life-cycle cost of alternative concrete mix designs, but rather gives additional insight for practitioners that analyze the risks associated with new-technology building materials and technologies. As such, this uncertainty analysis should be considered only useful to the advanced user of Life-365. See the users manual for more details.  Computing the uncertainty in service life may noticeably slow the performance of Life-365. To turn off uncertainty analysis, uncheck the "Compute uncertainty" box in the Concrete Mixes tab.',
          
          buttons: true,
          dangerMode: true,
          
        }).then((result) => {
          if (result) {
            
            this.form.useUncertainty = true
            this.needToCompute = true
          } else {
            
            this.form.useUncertainty = false
            this.needToCompute = false
          }
        })
      } else {
        
        this.needToCompute = true
      }
    },
    needToRegenerateGraphsAgain() {
      
      this.needToRegenerateGraphs = true
      let rows = document.getElementById('compute');
      let cells=rows.getElementsByTagName('td');
      for (let i=0; i<cells.length; i++) {
          if(i!=0 && i!=2){ 
            cells[i].innerHTML = ' <span class="text-sm font-medium">need to compute</span>';
          }
        }
    },
    valuesHaveChanged() {
      
      this.form.alternatives[this.activeAlternativeIndex].alternative.concreteMixDesign.valuesHaveChanged = true
      this.needToRegenerateGraphsAgain()
    },
    
    toggleNeedToComputeThisAlternative() {
      this.toggleNeedToComputeAlternative = this.activeAlternativeIndex
      //console.log(this.toggleNeedToComputeAlternative)
    },
    // preparePlayground() {
    //   let typeOfStructure = this.form.structure
    //   this.form.trueThickness = parseFloat(this.projectData.project.projectData.structureDimensions.trueThickness).toFixed(2)
    //   this.form.clearCover = parseFloat(this.projectData.project.projectData.structureDimensions.clearCover).toFixed(2)
    //   this.getFigure(typeOfStructure)
    // },

    split(left, right, parts) {
      var result = [],
        delta = (right - left) / (parts - 1)
      while (left < right) {
        result.push(left)
        left += delta
      }
      result.push(right)
      return result
    },

    toggleGraphTab(graphName) {
      if (graphName == 'cross-section') {
        // this.preparePlayground()
      }
      this.visibleGraph = graphName
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
    // getRedefinedUnits(value, eps) {
    //   this.$store.state.ecoParameters = eps
    //   axios
    //     .post(`/redefine-units`, {
    //       current_system_identifier: this.$store.state.currentSystem,
    //       new_system_identifier: value,
    //       data: eps,
    //     })
    //     .then((response) => {
    //       const { data } = response
    //       //Storing to state
    //       this.$store.state.currentSystem = data.current_system
    //       this.$store.state.ecoParameters = data.ecoparameters
    //       //Setting data values
    //       this.concentrationUnitsOptions = data.conc_units
    //       this.area_unit = data.area_unit
    //       this.volume_unit = data.volume_unit
    //       this.weight_unit = data.weight_unit
    //       this.capacity_unit = data.capacity_unit
    //       //Setting form values
    //       this.form.concUnits = data.default_conc_unit
    //       this.form.ecoparameters = data.ecoparameters
    //       this.form.baseYear = data.ecoparameters.baseYear.value
    //       this.form.studyPeriod = data.ecoparameters.studyPeriod.value
    //       this.form.inflation = parseFloat(data.ecoparameters.inflation.value).toFixed(2)
    //       this.form.discount = parseFloat(data.ecoparameters.discount.value).toFixed(2)
    //       this.form.baseMixCost = parseFloat(data.ecoparameters.baseMixCost.value).toFixed(2)
    //       this.form.costBlackSteel = parseFloat(data.ecoparameters.costBlackSteel.value).toFixed(2)
    //       this.form.costEpoxy = parseFloat(data.ecoparameters.costEpoxy.value).toFixed(2)
    //       this.form.costStainless = parseFloat(data.ecoparameters.costStainless.value).toFixed(2)
    //       this.form.costMembrane = parseFloat(data.ecoparameters.costMembrane.value).toFixed(2)
    //       this.form.costSealer = parseFloat(data.ecoparameters.costSealer.value).toFixed(2)
    //       this.form.costInhibitor = parseFloat(data.ecoparameters.costInhibitor.value).toFixed(2)
    //       this.form.repairCost = parseFloat(data.ecoparameters.repairCost.value).toFixed(2)
    //       this.form.areaToRepairPct = parseFloat(data.ecoparameters.areaToRepairPct.value).toFixed(2)
    //       this.form.repairIntervalYrs = data.ecoparameters.repairIntervalYrs.value
    //     })
    // },
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
        console.log('if',max);
        swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
        entry = max
      } else if (entry && entry < min) {
        console.log('else',min);
        swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
        entry = min
      }
      // this.form.post(this.route('compute-servicelife'))
      this.valuesHaveChanged()
    },

    validateAndUpdateParamsforPercentage(parameter, field, entry, min, max) {
     
      
     if (entry && entry > max) {
       
       swal('Percent of  ' + field + ' must be between ' + min +'% and ' + max +'%')
       entry = max
     } else if (entry && entry < min) {
       
       swal('Percent of  ' + field + ' must be between ' + min +'% and ' + max +'%')
       entry = min
     }
     const FlyAsh = document.getElementById('FlyAsh').value
     
     const Slag = document.getElementById('Slag').value
    
     if(((FlyAsh/50) + (Slag/50)) > 1 ){
     
      swal('The values of Slag and Fly Ash cannot be such that the sun (%Fly Ash/50 + %Slag / 70) is greater than 1.0')
     }
     // this.form.post(this.route('compute-servicelife'))
     this.valuesHaveChanged()
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
        xAxis.push(this.projectData.project.projectData.alts.alt[i].alternative.name)
      }
      return xAxis
    },
    createGraph2Y() {
      let yAxis = []
      let initiationInYears = []
      let propagationInYears = []
      //insert initiationYears
      let iniColor = '#808080'
      let propColor = '#C0C0C0'
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        initiationInYears.push(this.projectData.project.projectData.alts.alt[i].alternative.concreteMixDesign.initiationInYears)
      }
      yAxis.push({
        label: 'Initiation Years',
        backgroundColor: iniColor,
        data: initiationInYears,
      })

      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        propagationInYears.push(this.projectData.project.projectData.alts.alt[i].alternative.concreteMixDesign.propagationInYears)
      }
      yAxis.push({
        label: 'Propagation Years',
        backgroundColor: propColor,
        data: propagationInYears,
      })

      return yAxis
    },
    InitConcVsDepthX() {
      let xAxis = []
      for (let j = 0; j < this.serviceLifeGraphs.InitConcVsDepth.data[this.projectData.project.projectData.alts.alt[0].alternative.name].length; j++) {
        xAxis.push(parseFloat(this.serviceLifeGraphs.InitConcVsDepth.data[this.projectData.project.projectData.alts.alt[0].alternative.name][j].d1).toFixed(2))
      }
      return xAxis
    },
    InitConcVsDepthY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.InitConcVsDepth.data[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.InitConcVsDepth.data[this.projectData.project.projectData.alts.alt[i].alternative.name][j].d2)
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
      return yAxis
    },
    InitConcVsDepthAtTimeX() {
      let xAxis = []
      for (let j = 0; j < this.serviceLifeGraphs.InitConcVsDepthAtTime.data[this.projectData.project.projectData.alts.alt[0].alternative.name].length; j++) {
        xAxis.push(parseFloat(this.serviceLifeGraphs.InitConcVsDepthAtTime.data[this.projectData.project.projectData.alts.alt[0].alternative.name][j].d1).toFixed(2))
      }
      
      return xAxis
    },
    InitConcVsDepthAtTimeY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.InitConcVsDepthAtTime.data[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.InitConcVsDepthAtTime.data[this.projectData.project.projectData.alts.alt[i].alternative.name][j].d2)
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
     
      return yAxis
    },
    ConcCharDiffusivityX() {
      let xAxis = []
      for (let j = 0; j < this.serviceLifeGraphs.ConcCharDiffusivity.series[this.projectData.project.projectData.alts.alt[0].alternative.name].length; j++) {
        xAxis.push(parseFloat(this.serviceLifeGraphs.ConcCharDiffusivity.series[this.projectData.project.projectData.alts.alt[0].alternative.name][j].d1).toFixed(2))
      }
      
      return xAxis
    },
    ConcCharDiffusivityY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.ConcCharDiffusivity.series[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.ConcCharDiffusivity.series[this.projectData.project.projectData.alts.alt[i].alternative.name][j].d2)
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
      
      return yAxis
    },
    ConcCharSurfaceX() {
      let xAxis = []
      for (let j = 0; j < this.serviceLifeGraphs.ConcCharSurface.series[this.projectData.project.projectData.alts.alt[0].alternative.name].length; j++) {
        xAxis.push(parseFloat(this.serviceLifeGraphs.ConcCharSurface.series[this.projectData.project.projectData.alts.alt[0].alternative.name][j].d1).toFixed(2))
      }
      console.log('x=>',xAxis);
      return xAxis
    },
    ConcCharSurfaceY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.ConcCharSurface.series[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.ConcCharSurface.series[this.projectData.project.projectData.alts.alt[i].alternative.name][j].d2)
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
      console.log('y=>',yAxis);
      
      return yAxis
    },
    InitPeriodProbByYearX() {
      let xAxis = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150]
      return xAxis
    },
    InitPeriodProbByYearY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.InitPeriodProbByYear.results[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.InitPeriodProbByYear.results[this.projectData.project.projectData.alts.alt[i].alternative.name][j])
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
      return yAxis
    },
    CumulativeProbByYearX() {
      let xAxis = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150]
      return xAxis
    },
    CumulativeProbByYearY() {
      let yAxis = []
      let formedData = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        for (let j = 0; j < this.serviceLifeGraphs.CumulativeProbByYear.results[this.projectData.project.projectData.alts.alt[i].alternative.name].length; j++) {
          formedData.push(this.serviceLifeGraphs.CumulativeProbByYear.results[this.projectData.project.projectData.alts.alt[i].alternative.name][j])
        }
        yAxis.push({
          label: this.projectData.project.projectData.alts.alt[i].alternative.name,
          backgroundColor: this.altColors[i],
          fill: false,
          borderColor: this.altColors[i],
          data: formedData,
        })
      }
      return yAxis
    },
    InitVariationX() {
      let xAxis = []
      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        xAxis.push(this.projectData.project.projectData.alts.alt[i].alternative.name)
      }
      return xAxis
    },
    InitVariationY() {
      let yAxis = []
      let d28 = []
      let m = []
      let maxCS = []
      let ct = []
      let coverDepth = []
      //insert initiationYears
      let d28Color = '#808080'
      let mColor = '#7FB3D5'
      let maxCSColor = '#F1948A'
      let ctColor = '#76D7C4'
      let coverDepthColor = '#AF7AC5'

      for (let i = 0; i < this.projectData.project.projectData.alts.alt.length; i++) {
        d28.push(this.serviceLifeGraphs.InitVariation.results[this.projectData.project.projectData.alts.alt[i].alternative.name][0].value)
        m.push(this.serviceLifeGraphs.InitVariation.results[this.projectData.project.projectData.alts.alt[i].alternative.name][1].value)
        maxCS.push(this.serviceLifeGraphs.InitVariation.results[this.projectData.project.projectData.alts.alt[i].alternative.name][2].value)
        ct.push(this.serviceLifeGraphs.InitVariation.results[this.projectData.project.projectData.alts.alt[i].alternative.name][3].value)
        coverDepth.push(this.serviceLifeGraphs.InitVariation.results[this.projectData.project.projectData.alts.alt[i].alternative.name][4].value)
      }

      yAxis.push({
        label: 'd28',
        backgroundColor: d28Color,
        data: d28,
      })

      yAxis.push({
        label: 'm',
        backgroundColor: mColor,
        data: m,
      })

      yAxis.push({
        label: 'maxCS',
        backgroundColor: maxCSColor,
        data: maxCS,
      })

      yAxis.push({
        label: 'ct',
        backgroundColor: ctColor,
        data: ct,
      })

      yAxis.push({
        label: 'coverDepth',
        backgroundColor: coverDepthColor,
        data: coverDepth,
      })

      return yAxis
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
