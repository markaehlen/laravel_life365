<template>
  <div id="faqs">
    <trashed-message v-if="sublocation.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore"> This location has been deleted. </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Sub-Location</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.sublocations.index')" class="text-gray-700"> Sub-Locations </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Edit</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white">
      <form @submit.prevent="update">
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <h3 class="font-semibold"><u>Basic Details</u></h3>
        </div>
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Name" />
          <select-input v-model="form.location_id" :error="form.errors.location_id" class="pr-5 pb-2 w-full lg:w-1/4" :required="true" :label="'Parent Location'">
            <option v-for="(location, index) in locationOptions" :key="index" :value="index">{{ location }}</option>
          </select-input>
          <div class="pr-5 pb-2 w-full lg:w-1/4">
            <label class="form-label text-sm font-semibold" for="exposures">Exposures<span class="text-red-500 text-xs italic">*</span></label>
            <multiselect :multiple="true" :close-on-select="true" :clear-on-select="false" placeholder="Pick Options" label="label" track-by="label" :preselect-first="true" v-model="form.exposures" :options="exposureOptions" ref="exposures" v-bind="$attrs" :class="{ error: form.errors.exposures }"></multiselect>
            <div v-if="form.errors.exposures" class="form-error">{{ form.errors.exposures }}</div>
          </div>
          <number-input v-model="form.avg_temp" :step="'any'" :error="form.errors.avg_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Average Temperature (°C)" />
        </div>
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <h3 class="font-semibold"><u>Temperature Data</u> (°C)</h3>
        </div>
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <number-input v-model="form.jan_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.jan_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="January" />
          <number-input v-model="form.feb_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.feb_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="February" />
          <number-input v-model="form.mar_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.mar_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="March" />
          <number-input v-model="form.apr_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.apr_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="April" />
          <number-input v-model="form.may_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.may_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="May" />
          <number-input v-model="form.jun_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.jun_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="June" />
          <number-input v-model="form.jul_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.jul_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="July" />
          <number-input v-model="form.aug_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.aug_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="August" />
          <number-input v-model="form.sep_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.sep_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="September" />
          <number-input v-model="form.oct_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.oct_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="October" />
          <number-input v-model="form.nov_temp" :min="-80" :max="60" :step="'any'" :error="form.errors.nov_temp" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="November" />
          <number-input v-model="form.cost_sealer" :step="'any'" :min="0" :error="form.errors.cost_sealer" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Sealer ($)" />
        </div>
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <h3 class="font-semibold"><u>Other Related Data</u></h3>
        </div>
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <select-input v-model="form.is_marine" :error="form.errors.is_marine" class="pr-5 pb-2 w-full lg:w-1/4" :required="true" :label="'Is Marine?'">
            <option v-for="(marine, index) in isMarineOptions" :key="index" :value="index">{{ marine }}</option>
          </select-input>
          <number-input v-model="form.snow_day" :min="0" :max="365" :step="'1'" :error="form.errors.snow_day" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Snow Days" />
          <number-input v-model="form.mean_rh_perc" :step="'any'" :min="0" :max="100" :error="form.errors.mean_rh_perc" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Mean RH (%)" />
          <number-input v-model="form.build_up" :step="'any'" :min="0" :max="100" :error="form.errors.build_up" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Build Up (%)" />
          <number-input v-model="form.std_dev" :step="'any'" :min="0" :max="100" :error="form.errors.std_dev" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Standard Deviation (%)" />
          <number-input v-model="form.max_cs" :step="'any'" :min="0" :max="100" :error="form.errors.max_cs" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Max CS (%)" />
          <number-input v-model="form.cost_zerofourtwo" :step="'any'" :min="0" :error="form.errors.cost_zerofourtwo" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of 0.42 ($)" />
          <number-input v-model="form.cost_black" :step="'any'" :min="0" :error="form.errors.cost_black" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Black Steel ($)" />
          <number-input v-model="form.cost_stainless" :step="'any'" :min="0" :error="form.errors.cost_stainless" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Stainless Steel ($)" />
          <number-input v-model="form.cost_epoxy" :step="'any'" :min="0" :error="form.errors.cost_epoxy" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Epoxy Steel ($)" />
          <number-input v-model="form.membrane" :step="'any'" :min="0" :error="form.errors.membrane" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Membrane ($)" />
          <number-input v-model="form.cost_sealer" :step="'any'" :min="0" :error="form.errors.cost_sealer" :required="true" class="pr-5 pb-2 w-full lg:w-1/4" label="Cost of Sealer ($)" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.sublocations.index')" />
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert'
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import TextInput from '@admin/Shared/TextInput'
import NumberInput from '@admin/Shared/NumberInput'
import ActionBack from '@admin/Shared/ActionBack'
import TextareaInput from '@admin/Shared/TextareaInput'
import SelectInput from '@admin/Shared/SelectInput'
import LoadingButton from '@admin/Shared/LoadingButton'
import TrashedMessage from '@admin/Shared/TrashedMessage'
import Multiselect from 'vue-multiselect'

export default {
  metaInfo() {
    return {
      name: this.form.name,
    }
  },
  components: {
    Icon,
    TextInput,
    ActionBack,
    SelectInput,
    TextareaInput,
    LoadingButton,
    NumberInput,
    TrashedMessage,
    Multiselect,
  },
  layout: Layout,
  props: {
    sublocation: Object,
    locations: Object,
    exposures: Array,
    authorize: Object,
  },
  remember: 'form',
  data() {
    return {
      locationOptions: this.locations,
      exposureOptions: this.exposures,
      isMarineOptions: {
        1: 'Yes',
        0: 'No',
      },
      form: this.$inertia.form({
        _method: 'put',
        id: this.sublocation.id,
        name: this.sublocation.name,
        is_marine: this.sublocation.is_marine,
        jan_temp: this.sublocation.jan_temp,
        feb_temp: this.sublocation.feb_temp,
        mar_temp: this.sublocation.mar_temp,
        apr_temp: this.sublocation.apr_temp,
        may_temp: this.sublocation.may_temp,
        jun_temp: this.sublocation.jun_temp,
        jul_temp: this.sublocation.jul_temp,
        aug_temp: this.sublocation.aug_temp,
        sep_temp: this.sublocation.sep_temp,
        oct_temp: this.sublocation.oct_temp,
        nov_temp: this.sublocation.nov_temp,
        dec_temp: this.sublocation.dec_temp,
        avg_temp: this.sublocation.avg_temp,
        snow_day: this.sublocation.snow_day,
        mean_rh_perc: this.sublocation.mean_rh_perc,
        build_up: this.sublocation.build_up,
        std_dev: this.sublocation.std_dev,
        max_cs: this.sublocation.max_cs,
        cost_zerofourtwo: this.sublocation.cost_zerofourtwo,
        cost_black: this.sublocation.cost_black,
        cost_stainless: this.sublocation.cost_stainless,
        cost_epoxy: this.sublocation.cost_epoxy,
        membrane: this.sublocation.membrane,
        cost_sealer: this.sublocation.cost_sealer,
        location_id: this.sublocation.location.id,
        exposures: this.sublocation.exposures,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.sublocations.update', this.sublocation.id), {
        onSuccess: () => this.form.reset(),
      })
    },
    restore() {
      const vInstance = this
      if (!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this sub-location?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.sublocations.restore', vInstance.sublocation.id))
        }
      })
    },
  },
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
