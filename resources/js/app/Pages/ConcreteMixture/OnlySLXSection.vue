<template>
    <KonvaSLXSectionPlot 
        :plotType="structure"
        :projectData="projectData.project.projectData"
        :units="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].units"
        :concUnits="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].concentrationUnits"
        :maxConc="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].maxConcentration"
        :allPoints="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].allPoints"
        :thickness="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].overallThickness"
        :clearCover="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].clearCover"
        :iCurrentlyDisplayedMonth="this.serviceLifeResults.results['Cross-Section Graph'].results[activeAlternativeName].iMonthOfInit"
        :justSection="false"
    />
  </template>
  
<script>
import KonvaSLXSectionPlot from '@app/Shared/KonvaSLXSectionPlot'

export default {
  metaInfo: { title: 'Concrete Mixtures' },
  components: {
    KonvaSLXSectionPlot,
  },
//   layout: Layout,
  props: {
    projectData: Object,
    serviceLifeResults: Object,
    rebarSteelTypes: Object,
    inhibitors: Object,
    barrierTypes: Object,
    colors: [Object, Array],
  },
  computed: {},
  mounted() {
  },
  created() {},
  data() {
    return {
      userAgent: this.$page.props.isMobile,
      //All Units
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      //Select Options
      rebarSteelTypesOptions: this.rebarSteelTypes,
      inhibitorsOptions: this.inhibitors,
      serviceLifeGraphs: this.serviceLifeResults.results,
    //   baseUnit: this.serviceLifeResults.results['Cross-Section Graph'].results['Sample 1'].units,
        structure: this.projectData.project.projectData.typeOfStructure,
        // baseUnits: this.projectData.project.projectData.baseUnits,
        concUnits: this.projectData.project.projectData.concentrationUnits,
        alternatives: this.projectData.project.projectData.alts.alt,
        activeAlternativeName: this.projectData.project.projectData.alts.alt[0].alternative.name,
        activeAlternativeDesc: this.projectData.project.projectData.alts.alt[0].alternative.description,
      barrierTypesOptions: this.barrierTypes,
      visibleGraph: 'service-life',
      defaultColDef: {
        flex: 1,
      },
      altColors: this.colors,
      activeAlternativeIndex: 0,
      needToRegenerateGraphs: false,
      needToCompute: false,
    }
  },
  methods: {
   
  },
}
</script>
