<template>
  <div id="faqs">
    <trashed-message v-if="unit.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore"> This location has been deleted. </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Sub-location</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.units.index')" class="text-gray-700"> Sub-locations </inertia-link>
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
    <div class="bg-white overflow-hidden">
      <form @submit.prevent="update">
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
import swal from 'sweetalert'
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import TextInput from '@admin/Shared/TextInput'
import ActionBack from '@admin/Shared/ActionBack'
import TextareaInput from '@admin/Shared/TextareaInput'
import SelectInput from '@admin/Shared/SelectInput'
import LoadingButton from '@admin/Shared/LoadingButton'
import TrashedMessage from '@admin/Shared/TrashedMessage'

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
    TrashedMessage,
  },
  layout: Layout,
  props: {
    unit: Object,
    systems: Object,
    unit_types: Array,
    authorize: Object,
  },
  remember: 'form',
  data() {
    return {
      typeOptions: this.unit_types,
      systemOptions: this.systems,
      form: this.$inertia.form({
        _method: 'put',
        id: this.unit.id,
        name: this.unit.name,
        short_hand: this.unit.short_hand,
        system_id: this.unit.system.id,
        type: this.unit.type,
        conversion_factor: this.unit.conversion_factor,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.units.update', this.unit.id), {
        onSuccess: () => this.form.reset(),
      })
    },
    restore() {
      const vInstance = this
      if (!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this unit?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.units.restore', vInstance.unit.id))
        }
      })
    },
  },
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
