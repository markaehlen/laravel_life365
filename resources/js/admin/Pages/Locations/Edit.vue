<template>
  <div id="faqs">
    <trashed-message v-if="location.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore"> This location has been deleted. </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Location</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.locations.index')" class="text-gray-700"> Locations </inertia-link>
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
          <text-input v-model="form.name" :error="form.errors.name" :required="true" class="pr-5 pb-2 w-full lg:w-2/5" label="Title" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.locations.index')" />
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
    TextareaInput,
    LoadingButton,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    location: Object,
    authorize: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        id: this.location.id,
        name: this.location.name,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.locations.update', this.location.id), {
        onSuccess: () => this.form.reset(),
      })
    },
    restore() {
      const vInstance = this
      if (!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this location?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.locations.restore', vInstance.location.id))
        }
      })
    },
  },
}
</script>
