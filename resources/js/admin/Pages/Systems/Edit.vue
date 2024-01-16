<template>
  <div id="roles">
    <trashed-message v-if="role.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore">
      This role has been deleted.
    </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Role</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700">
                Home
              </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.roles.index')" class="text-gray-700">
                Role's
              </inertia-link>
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
          <text-input v-model="form.name" :error="form.errors.name" :required="true" class="pr-5 pb-2 w-full lg:w-2/6" label="Name" />
        </div>
        <h1 class="w-full bg-gray-200 text-gray-600 uppercase py-2 px-4 text-sm font-semibold">Permissions</h1>
        <div v-for="(module, index) in modules" :key="module.id" class="p-5 -mr-5 -mb-2 flex flex-wrap">
          <h1 class="text-sm leading-5 font-semibold py-2 px-2 bg-gray-200 text-gray-600 lg:w-1/12 mr-5">{{ module.name }}</h1>
          <label v-for="permission in permissions" :key="permission.id" class="inline-flex items-center lg:w-1/12">
            <input v-model="form.permissions[index]" type="radio" :value="{ module: module.id, permission: permission.id }" class="form-radio h-5 w-5 text-gray-600" />
            <span class="ml-2">{{ permission.name }}</span>
          </label>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.roles.index')" />
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
import LoadingButton from '@admin/Shared/LoadingButton'
import TrashedMessage from '@admin/Shared/TrashedMessage'

export default {
  metaInfo() {
    return {
      title: this.form.title,
    }
  },
  components: {
    Icon,
    TextInput,
    ActionBack,
    LoadingButton,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    role: Object,
    modules: Array,
    permissions: Array,
    assignedPermissions: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        id: this.role.id,
        name: this.role.name,
        permissions: this.assignedPermissions,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.roles.update', this.role.id), {
        onSuccess: () => this.form.reset(),
      })
    },
    restore() {
      const vInstance = this
      if(!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this role?', {
        buttons: ['No', 'Yes'],
      }).then(value => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.roles.restore', vInstance.role.id))
        }
      })
    },
  },
}
</script>
