<template>
  <div id="locations">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Add Unit</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.units.index')" class="text-gray-700"> Units </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Add</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white overflow-hidden">
      <form @submit.prevent="store">
        <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="Name" />
          <text-input v-model="form.short_hand" :error="form.errors.short_hand" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="Short Hand" />
          <select-input v-model="form.system_id" :error="form.errors.system_id" class="pr-5 pb-2 w-full lg:w-1/3" :required="true" :label="'Parent System'">
            <option v-for="(system, index) in systemOptions" :key="index" :value="index">{{ system }}</option>
          </select-input>
          <select-input v-model="form.type" :error="form.errors.type" class="pr-5 pb-2 w-full lg:w-1/3" :required="true" :label="'Type Of Unit'">
            <option v-for="(type, index) in typeOptions" :key="index" :value="index">{{ type }}</option>
          </select-input>
          <text-input :type="'number'" step="any" v-model="form.conversion_factor" :error="form.errors.conversion_factor" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="Conversion Factor" :help="'With respect to BASE STANDARD'" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.units.index')" />
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import TextInput from '@admin/Shared/TextInput'
import ActionBack from '@admin/Shared/ActionBack'
import TextareaInput from '@admin/Shared/TextareaInput'
import LoadingButton from '@admin/Shared/LoadingButton'
import SelectInput from '@admin/Shared/SelectInput.vue'

export default {
  metaInfo: { title: 'Add Unit' },
  components: {
    Icon,
    TextInput,
    SelectInput,
    ActionBack,
    TextareaInput,
    LoadingButton,
  },
  layout: Layout,
  remember: 'form',
  props: {
    systems: Object,
    unit_types: Array,
  },
  data() {
    return {
      systemOptions: this.systems,
      typeOptions: this.unit_types,
      form: this.$inertia.form({
        name: null,
        short_hand: null,
        system_id: null,
        type: null,
        conversion_factor: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('admin.units.store'))
    },
  },
}
</script>
