<template>
  <div id="preview">
    <trashed-message v-if="user.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore"> This user has been deleted. </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Users</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.users.index')" class="text-gray-700"> Users </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Preview</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full text-left">
        <tbody class="text-gray-600 text-sm font-light">
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Name</td>
            <td class="py-3 px-6">
              <span>{{ user.name }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Email</td>
            <td class="py-3 px-6">
              <span>{{ user.email }}</span>
            </td>
          </tr>
        
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Created At</td>
            <td class="py-3 px-6">
              <span>{{ user.created_at }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <action-back :href="route('admin.users.index')" />
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert'
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import ActionBack from '@admin/Shared/ActionBack'
import TrashedMessage from '@admin/Shared/TrashedMessage'

export default {
  name: 'UserPreview',
  metaInfo() {
    return {
      title: `${this.form.first_name} ${this.form.last_name}`,
    }
  },
  components: {
    Icon,
    ActionBack,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    user: Object,
    authorize: Object,
  },
  methods: {
    restore() {
      if (!this.authorize.restore) return
      const vInstance = this
      swal('Are you sure you want to restore this user?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.users.restore', vInstance.user.id))
        }
      })
    },
  },
}
</script>
