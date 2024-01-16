<!-- Defaults panel that user can access via the left menu, and can then set all of the default values -->
<template>
  <div id="default" class="document">
    <div
      class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 py-1 px-2.5">
      <h1 class="font-semibold text-2xl">Settings & Parameters</h1>
    </div>
    <div class="bg-white overflow-hidden1">
      <ValidationObserver ref="form">
        <form @submit.prevent="store">
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold mb-2 lg:mb-0 md:mb-0">Default Units Of Measure</h3>
            <div class="flex">
              <loading-button id="save-button" :loading="form.processing" type="submit"
                class="btn-indigo-flat mr-2">OK</loading-button>
            </div>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 md:grid-cols-2 gap-4 p-2.5">
            <select-input v-model="form.baseUnits" :rules="`required`" :title="`Choose base system units`"
              :tool-tip-text="helping_tips.base_units" :error="form.errors.baseUnits" class="selectTyp" :required="true"
              :label="'Base Units'" @input="getRedefinedUnits($event, ecoParameters)">
              <option v-for="(system, index) in systemsOptions" :key="index" :value="index">{{ system }}</option>
            </select-input>
            <select-input v-model="form.concUnits" :rules="`required`" :title="`Select concentration units`"
              :error="form.errors.concUnits" class="selectTyp" :required="true" :label="'Concentration Units'"
              :tool-tip-text="helping_tips.concentration_units">
              <option v-for="(concentration_unit, index) in concentrationUnitsOptions" :key="index" :value="index">{{
                concentration_unit }}</option>
            </select-input>
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Region and Exposure Conditions</h3>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-4 p-2.5">
            <select-input id="location" v-model="form.location" :rules="`required`" :title="`Select location`"
              :error="form.errors.location" class="selectTyp" :required="true" :label="'Location'"
              :tool-tip="helping_tips.location" @input="getSublocations($event)">
              <option v-for="(location, index) in locationOptions" :key="index" :value="index">{{ location }}</option>
            </select-input>
            <select-input id="sub-location" v-model="form.subLocation" :rules="`required`" :title="`Select Sub-location`"
              :error="form.errors.subLocation" class="selectTyp" :required="true" :label="'Sub-locations'"
              :tool-tip-text="helping_tips.sub_locations" @input="getExposures($event)">
              <option v-for="(sublocation, index) in sublocationOptions" :key="index" :value="index">{{ sublocation }}
              </option>
            </select-input>
            <select-input id="exposure" v-model="form.exposureType" :rules="`required`" :title="`Select exposure type`"
              :error="form.errors.exposureType" class="selectTyp" :required="true" :label="'Exposure'"
              :tool-tip-text="helping_tips.exposure_default_settings">
              <option v-for="(exposure, index) in exposureOptions" :key="index" :value="index">{{ exposure }}</option>
            </select-input>
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Economic Parameters</h3>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-4 gap-4 p-2.5">
            <number-input v-model="form.baseYear" :tool-tip-text="helping_tips.base_year_default_settings" :min="2000"
              :max="`${new Date().getFullYear() + 2}`"
              :rules="`required|min_value:2000|max_value:${new Date().getFullYear() + 2}`"
              :title="`Allowable range of values is [2000 , ${new Date().getFullYear() + 2}]`" step="1"
              :error="form.errors.baseYear" :required="true" class="selectTyp" label="Base Year (e.g. 2023)"
              @blur="validateAndUpdateEcoParams('baseYear', 'Base Year', $event, 2000, new Date().getFullYear() + 2)" />
            <number-input v-model="form.studyPeriod" :tool-tip-text="helping_tips.analysis_period_default_settings"
              :min="1" :max="500" :rules="`required|min_value:1|max_value:500`"
              :title="`Allowable range of values is [1 , 500]`" step="1" :error="form.errors.studyPeriod" :required="true"
              class="selectTyp mb-2.5" label="Analysis Period (yrs)"
              @blur="validateAndUpdateEcoParams('studyPeriod', 'Study Period', $event, 1, 500)" />
            <number-input v-model="form.inflation" :tool-tip-text="helping_tips.inflation_rate_default_settings" :min="0"
              :max="20" :rules="`required|min_value:0|max_value:20`" :title="`Allowable range of values is [0% , 20%]`"
              step="any" :error="form.errors.inflation" :required="true" class="selectTyp mb-2.5"
              label="Inflation Rate (%)" @blur="validateAndUpdateEcoParams('inflation', 'Inflation', $event, 0, 20)" />
            <number-input v-model="form.discount" :tool-tip-text="helping_tips.discount_rate_default_settings" :min="0"
              :max="20" :rules="`required|min_value:0|max_value:20`" :title="`Allowable range of values is [0% , 20%]`"
              step="any" :error="form.errors.discount" :required="true" class="selectTyp mb-2.5" label="Discount Rate (%)"
              @blur="validateAndUpdateEcoParams('discount', 'Discount Rate', $event, 0, 20)" />
          </div>
          <div class="grey-bg-block grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-4 p-2.5">
            <div class="colGrey">
              <h5 class="w-full font-semibold"><u>Concrete & Steel</u></h5>
              <number-input v-model="form.baseMixCost" :tool-tip-text="helping_tips.concrete_default_settings" :min="0"
                :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.baseMixCost"
                :required="true" class="selectTyp mb-2.5" :label="'Concrete ($/' + volume_unit + ')'"
                @blur="validateAndUpdateEcoParams('baseMixCost', 'Base Mix Cost', $event, 0, 10000)" />
              <number-input v-model="form.costBlackSteel" :tool-tip-text="helping_tips.black_steel_default_settings"
                :min="0" :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costBlackSteel"
                :required="true" class="selectTyp mb-2.5" :label="'Black Steel ($/' + weight_unit + ')'"
                @blur="validateAndUpdateEcoParams('costBlackSteel', 'Black Steel Cost', $event, 0, 10000)" />
              <number-input v-model="form.costEpoxy" :tool-tip-text="helping_tips.epoxy_coated_steel_default_settings"
                :min="0" :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costEpoxy"
                :required="true" class="selectTyp mb-2.5" :label="'Epoxy Coated Steel ($/' + weight_unit + ')'"
                @blur="validateAndUpdateEcoParams('costEpoxy', 'Epoxy Steel Cost', $event, 0, 10000)" />
              <number-input v-model="form.costStainless" :tool-tip-text="helping_tips.stainless_steel_default_settings"
                :min="0" :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costStainless"
                :required="true" class="selectTyp mb-2.5" :label="'Stainless Steel ($/' + weight_unit + ')'"
                @blur="validateAndUpdateEcoParams('costStainless', 'Stainless Steel Cost', $event, 0, 10000)" />
            </div>
            <div class="colGrey">
              <h5 class="pr-5 w-full font-semibold"><u>Barriers & Inhibitors</u></h5>
              <number-input v-model="form.costMembrane" :tool-tip-text="helping_tips.membrane_default_settings" :min="0"
                :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costMembrane"
                :required="true" class="selectTyp mb-2.5" :label="'Membrane ($/' + area_unit + ')'"
                @blur="validateAndUpdateEcoParams('costMembrane', 'Membrane Cost', $event, 0, 10000)" />
              <number-input v-model="form.costSealer" :tool-tip-text="helping_tips.sealer_default_settings" :min="0"
                :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costSealer"
                :required="true" class="selectTyp mb-2.5" :label="'Sealer ($/' + area_unit + ')'"
                @blur="validateAndUpdateEcoParams('costSealer', 'Sealer Cost', $event, 0, 10000)" />
              <number-input v-model="form.costInhibitor" :tool-tip-text="helping_tips.inhibitor_default_settings" :min="0"
                :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.costInhibitor"
                :required="true" class="selectTyp mb-2.5" :label="'Inhibitor ($/' + capacity_unit + ')'"
                @blur="validateAndUpdateEcoParams('costInhibitor', 'Inhibitor Cost', $event, 0, 10000)" />
            </div>
            <div class="colGrey">
              <h5 class="selectTyp font-semibold"><u>Repairs</u></h5>
              <number-input v-model="form.repairCost" :tool-tip-text="helping_tips.repair_default_settings" :min="0"
                :max="10000" :rules="`required|min_value:0|max_value:10000`"
                :title="`Allowable range of values is [0 , 10000]`" step="any" :error="form.errors.repairCost"
                :required="true" class="selectTyp mb-2.5" :label="'Repair ($/' + area_unit + ')'"
                @blur="validateAndUpdateEcoParams('repairCost', 'Repair Cost', $event, 0, 10000)" />
              <number-input v-model="form.areaToRepairPct" :tool-tip-text="helping_tips.area_to_repair_default_settings"
                :min="0.1" :max="100" :rules="`required|min_value:0.1|max_value:100`"
                :title="`Allowable range of values is [0.1 , 100]`" step="any" :error="form.errors.areaToRepairPct"
                :required="true" class="selectTyp mb-2.5" label="Area to repair (%)"
                @blur="validateAndUpdateEcoParams('areaToRepairPct', 'Area to repair percentage', $event, 0.1, 100)" />
              <number-input v-model="form.repairIntervalYrs"
                :tool-tip-text="helping_tips.fixed_repair_intervals_default_settings" :min="1" :max="500"
                :rules="`required|min_value:1|max_value:500`" :title="`Allowable range of values is [1 , 500]`" step="1"
                :error="form.errors.repairIntervalYrs" :required="true" class="selectTyp mb-2.5"
                label="Fixed repair intervals (yrs)"
                @blur="validateAndUpdateEcoParams('repairIntervalYrs', 'Repair Interval', $event, 1, 500)" />
            </div>
          </div>
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 py-1 px-2.5">
            <h3 class="font-semibold">Display Colors of Alternatives</h3>
          </div>
          <div class="p-2.5 circlesBlock justify-between flex flex-wrap items-center">
            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Base Case</h5>
              <v-swatches v-model="form.base_alt_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>

            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Alternative 1</h5>
              <v-swatches v-model="form.alt1_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>

            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Alternative 2</h5>
              <v-swatches v-model="form.alt2_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>

            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Alternative 3</h5>
              <v-swatches v-model="form.alt3_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>

            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Alternative 4</h5>
              <v-swatches v-model="form.alt4_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>

            <div class="flex items-center justify-between mobile-flex-full mb-2 lg:mb-0 md:mb-0">
              <h5 class="pr-2 font-semibold">Alternative 5</h5>
              <v-swatches v-model="form.alt5_color" class="pr-5" shapes="circles" show-border popover-x="right" />
            </div>
          </div>
          <div class="w-full border-b lg:flex md:flex justify-between items-center bg-gray-50 py-1 px-2.5">
            <span style="font-weight: 500"
              class="btn-indigo-flat mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-600 border reset-btn-mobile"
              @click="resetDefaults()">Reset to Life-365™ default values</span>
            <div class="flex">
              <loading-button id="save-button" :loading="form.processing" type="submit"
                class="btn-indigo-flat mr-2">OK</loading-button>
            </div>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </div>
</template>

// procedural code
<script>

// import other controls
import swal from 'sweetalert'
import Icon from '@app/Shared/Icon'
import Layout from '@app/Shared/Layout'
import NumberInput from '@app/Shared/NumberInput'
import LoadingButton from '@app/Shared/LoadingButton'
import SelectInput from '@app/Shared/SelectInput.vue'
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import 'v-tooltip/dist/v-tooltip.css'
import { EventBus } from '@app/event-bus'

// create the control
export default {
  metaInfo: { title: 'Default Settings' },
  components: {
    Icon,
    NumberInput,
    SelectInput,
    LoadingButton,
    VSwatches,
  },
  layout: Layout,
  remember: 'form',

  // declare the variables/properties in the control
  props: {
    systems: Object,
    concentrationUnits: Object,
    locations: Object,
    ecoparameters: Object,
    baseUnits: String,
    concUnits: String,
    location: String,
    subLocation: String,
    exposureType: String,
    base_alt_color: String,
    alt1_color: String,
    alt2_color: String,
    alt3_color: String,
    alt4_color: String,
    alt5_color: String,
    helping_tips: Object,
  },
  mounted() {
    EventBus.$on("navigateApplicationTo", (data) => {
      this.form.intendedUrl = data;
      // EventBus.$off("navigateApplicationTo");
      this.store()
    });
  },

  // getter: values that are returned in a 'data' request
  data() {
    return {
      //All Units
      area_unit: null,
      volume_unit: null,
      capacity_unit: null,
      weight_unit: null,
      //Select Options
      systemsOptions: this.systems,
      locationOptions: this.locations,
      concentrationUnitsOptions: null,
      sublocationOptions: null,
      exposureOptions: null,

      // setter: set the values of the control's variables/properties
      form: this.$inertia.form({
        intendedUrl: 'set-project',
        baseUnits: this.baseUnits,
        concUnits: this.concUnits,

        //Locations specific parameters
        location: this.location,
        subLocation: this.subLocation,
        exposureType: this.exposureType,
        ecoparameters: this.ecoparameters,

        // Economic parameters
        baseYear: this.ecoparameters.baseYear.value,
        studyPeriod: this.ecoparameters.studyPeriod.value,

        // CODE ISSUE: Why are we downgrading the precision of these variables?
        ////////////////////////
        inflation: parseFloat(this.ecoparameters.inflation.value).toFixed(2),
        discount: parseFloat(this.ecoparameters.discount.value).toFixed(2),
        baseMixCost: parseFloat(this.ecoparameters.baseMixCost.value).toFixed(2),
        costBlackSteel: parseFloat(this.ecoparameters.costBlackSteel.value).toFixed(2),
        costEpoxy: parseFloat(this.ecoparameters.costEpoxy.value).toFixed(2),
        costStainless: parseFloat(this.ecoparameters.costStainless.value).toFixed(2),
        costMembrane: parseFloat(this.ecoparameters.costMembrane.value).toFixed(2),
        costSealer: parseFloat(this.ecoparameters.costSealer.value).toFixed(2),
        costInhibitor: parseFloat(this.ecoparameters.costInhibitor.value).toFixed(2),
        repairCost: parseFloat(this.ecoparameters.repairCost.value).toFixed(2),
        areaToRepairPct: parseFloat(this.ecoparameters.areaToRepairPct.value).toFixed(2),
        ////////////////////////
        repairIntervalYrs: this.ecoparameters.repairIntervalYrs.value,

        //Colors
        base_alt_color: this.base_alt_color,
        alt1_color: this.alt1_color,
        alt2_color: this.alt2_color,
        alt3_color: this.alt3_color,
        alt4_color: this.alt4_color,
        alt5_color: this.alt5_color,
      }),
    }
  },
  computed: {
    ecoParameters() {
      return this.$store.state.ecoParameters
    },
  },
  created() {
    let eps = this.$props.ecoparameters
    let system = this.$props.baseUnits
    let defaultLocation = this.$props.location
    this.$store.state.currentSystem = system
    this.getRedefinedUnits(system, eps)
    this.getSublocations(defaultLocation)
  },
  methods: {
    store() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return
        }
        this.form.post(this.route('save.defaults'))
      })
    },
    resetDefaults() {
      this.$store.state.currentSystem = null
      this.$store.state.ecoParameters = null
      window.location.replace('/reset-defaults')
    },

    // basically a 'gather' function, where we get the values of the variables/properties in the control
    getRedefinedUnits(value, eps) {

      this.$store.state.ecoParameters = eps
      axios
        .post('/redefine-units', {
          current_system_identifier: this.$store.state.currentSystem,
          new_system_identifier: value,
          current_conc_unit: this.form.concUnits,
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
    assignIntendedUrl() {

      alert('sample');
      this.form.intendedUrl = document.getElementById('navigateApplicationTo').value
      this.store()
    },

    getSublocations(value) {

      axios.get(`/get-sublocations/${value}`).then((response) => {

        const { data } = response;
        this.sublocationOptions = data.sublocations;
        this.form.subLocation = data.default_value;
        // Handle the case where no options are available if needed.
        this.getExposures();
      });
    },

    getExposures() {

      axios.get(`/get-exposures/${this.form.subLocation}`).then((response) => {

        const { data } = response
        this.exposureOptions = data.exposures
        if (Object.values(this.exposureOptions).indexOf(this.form.exposureType) < 0) {

          this.form.exposureType = data.default_value
        }
      })
    },

    validateAndUpdateEcoParams(ecoparameter, field, entry, min, max) {

      let temporaryValue = null
      Object.keys(this.$store.state.ecoParameters).forEach(function (key, index) {

        if (key === ecoparameter) {

          if (entry && entry > max) {

            swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
            entry = max
          } else if (entry && entry < min) {

            swal('Allowable range of values for ' + field + ' is [' + min + ' , ' + max + ']')
            entry = min
          }

          if (ecoparameter == 'baseYear' || ecoparameter == 'studyPeriod' || ecoparameter == 'repairIntervalYrs') {

            temporaryValue = this[key].value = entry
          } else {

            temporaryValue = this[key].value = parseFloat(entry).toFixed(2)
          }
        }
      }, this.$store.state.ecoParameters)
      this.form[ecoparameter] = temporaryValue
    },
  },
}

</script>
<style>
.v-popper--theme-info-dropdown .v-popper__inner {
  background: #004499;
}

.v-popper--theme-info-dropdown .v-popper__arrow {
  border-color: #004499;
}

.v-popper--theme-info-tooltip {
  border-color: #004499;
  background: #004499;
}
</style>
