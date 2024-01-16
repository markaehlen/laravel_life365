<template>
  <div id="faqs">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Manage Economic Parameters</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Economic Parameters</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="mb-5 block 2xl:flex xl:flex lg:flex md:flex justify-between bg-gray-50 py-5 px-5 items-center border-b">
      <search-box v-model="form.search" class="w-full max-w-md mr-4" @reset="reset" />
      <!-- <action-add v-if="authorize.create" :href="route('admin.ecoparameters.create')" title="Economic Paramater" /> -->
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <!-- <th class="py-3 px-6">Sr. No.</th> -->
            <Sortable v-model="form.sort" class="py-3 px-6" label="Name" column="name" :sort="form.sort" />
            <th class="py-3 px-6">API Identifier</th>
            <th class="py-3 px-6">Defaults</th>
            <!-- <Sortable v-model="form.sort" class="py-3 px-6" label="Date" column="created_at" :sort="form.sort" /> -->
            <!-- <th class="py-3 px-6 text-center">Actions</th> -->
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="ecoparameter in ecoparameters.data" :key="ecoparameter.id" class="border-b border-gray-200 hover:bg-gray-100">
            <!-- <td class="py-3 px-6"> -->
            <!-- <span class="text-sm font-medium text-gray-900">{{ ecoparameter.srno }}</span> -->
            <!-- </td> -->
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ ecoparameter.name }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ ecoparameter.api_identifier }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">
                <span v-if="ecoparameter.type && ecoparameter.type !== 'percentage'"> $</span>{{ ecoparameter.default_value }}<span v-if="ecoparameter.type && ecoparameter.type !== 'percentage'"> / </span><span v-if="ecoparameter.default_unit"> {{ ecoparameter.default_unit.short_hand }}</span></span
              >
            </td>
            <!-- <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ ecoparameter.created_at }}</span>
            </td> -->
            <!-- <td class="py-3 px-6">
              <div class="flex item-center justify-center"> -->
            <!-- <action-preview v-if="authorize.view" :href="route('admin.ecoparameters.show', ecoparameter.id)" title="View" /> -->
            <!-- <action-edit v-if="authorize.update" :href="route('admin.ecoparameters.edit', ecoparameter.id)" title="Edit" /> -->
            <!-- <action-delete v-if="!ecoparameter.deleted_at && authorize.delete" title="Delete" @delete="destroy(ecoparameter)" /> -->
            <!-- </div> -->
            <!-- </td> -->
          </tr>
          <tr v-if="ecoparameters.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4"><b>No economic parameters found.</b></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="ecoparameters.links" />
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
import SearchBox from '@admin/Shared/SearchBox'
import ActionPreview from '@admin/Shared/ActionPreview'
import ToggleStatus from '@admin/Shared/ToggleStatus.vue'

export default {
  metaInfo: { title: 'Economic Parameters' },
  components: {
    Icon,
    Sortable,
    ActionAdd,
    ActionEdit,
    Pagination,
    ActionDelete,
    SearchBox,
    ActionPreview,
    ToggleStatus,
  },
  layout: Layout,
  props: {
    ecoparameters: Object,
    filters: Object,
    authorize: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
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
        this.$inertia.replace(this.route('admin.ecoparameters.index', query))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    destroy(item) {
      const vInstance = this
      if (!vInstance.authorize.delete) return
      swal('Are you sure you want to delete this economic parameter?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.delete(vInstance.route('admin.ecoparameters.destroy', item.id))
        }
      })
    },
    toggleStatus(item) {
      const vInstance = this
      if (!vInstance.authorize.update) return
      swal(`Are you sure you want to ${item.status ? 'de-activate' : 'activate'} this economic parameter?`, {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.ecoparameters.toggle.status', item.id))
        }
      })
    },
  },
}
</script>
