<template>
  <div id="users">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl mb-1">Manage Users</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Users</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="mb-5 block 2xl:flex xl:flex lg:flex md:flex justify-between bg-gray-50 py-5 px-5 items-center border-b">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="mt-4 block text-gray-700">Status:</label>
        <select v-model="form.status" class="mt-1 w-full form-select">
          <option :value="null">All</option>
          <option value="active">Active</option>
          <option value="in-active">In-active</option>
        </select>
        <!-- <label class="mt-4 block text-gray-700">Deleted:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null">All</option>
          <option value="with">With Deleted Users</option>
          <option value="only">Only Deleted Users</option>
        </select> -->
      </search-filter>
      <action-add class="add-btn-mobile" v-if="authorize.create" :href="route('admin.users.create')" title="User" />
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <Sortable v-model="form.sort" class="py-3 px-6" label="User" column="email" :sort="form.sort" />
            <th class="py-3 px-6">Status</th>
            <th class="py-3 px-6">Member Since</th>
            <th class="py-3 px-6 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="user in users.data" :key="user.id" class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6 whitespace-nowrap">
              <div class="flex items-center">
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ user.email }}
                  </div>
                </div>
              </div>
            </td>
            <td class="py-3 px-6">
              <toggle-status :status="user.is_active" @toggleStatus="toggleStatus(user)" />
            </td>
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ user.created_at }}</span>
            </td>
            <td class="py-3 px-6">
              <div class="flex item-center justify-center">
                <action-preview v-if="authorize.view" :href="route('admin.users.show', user.id)" title="View" />
                <action-edit v-if="authorize.update" :href="route('admin.users.edit', user.id)" title="Edit" />
                <action-delete v-if="!user.deleted_at && authorize.delete" title="Delete" @delete="destroy(user)" />
              </div>
            </td>
          </tr>
          <tr v-if="users.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No enquiries found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="users.links" />
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert'
import pickBy from 'lodash/pickBy'
import Icon from '@admin/Shared/Icon'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Layout from '@admin/Shared/Layout'
import Sortable from '@admin/Shared/Sortable'
import ActionAdd from '@admin/Shared/ActionAdd'
import ActionEdit from '@admin/Shared/ActionEdit'
import Pagination from '@admin/Shared/Pagination'
import ActionDelete from '@admin/Shared/ActionDelete'
import SearchFilter from '@admin/Shared/SearchFilter'
import ActionPreview from '@admin/Shared/ActionPreview'
import ToggleStatus from '@admin/Shared/ToggleStatus.vue'

export default {
  metaInfo: { title: 'Users' },
  components: {
    Icon,
    Sortable,
    ActionAdd,
    ActionEdit,
    Pagination,
    ActionDelete,
    SearchFilter,
    ActionPreview,
    ToggleStatus,
  },
  layout: Layout,
  props: {
    users: Object,
    filters: Object,
    authorize: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        status: this.filters.status,
        trashed: this.filters.trashed,
        sort: this.filters.sort,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('admin.users.index', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    destroy({ id }) {
      const vInstance = this
      if (!vInstance.authorize.delete) return
      swal('Are you sure you want to delete this user?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.delete(this.route('admin.users.destroy', id))
        }
      })
    },
    toggleStatus({ id, is_active }) {
      const vInstance = this
      if (!vInstance.authorize.update) return
      swal(`Are you sure you want to ${is_active ? 'de-activate' : 'activate'} this user?`, {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.users.toggle.status', id))
        }
      })
    },
  },
}
</script>
