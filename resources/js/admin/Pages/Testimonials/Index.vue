<template>
  <div id="testimonials">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Manage Testimonial's</h1>
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
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Testimonial's</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="mb-5 flex justify-between bg-gray-50 py-5 px-5 items-center border-b">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Status:</label>
        <select v-model="form.status" class="mt-1 w-full form-select">
          <option :value="null">All</option>
          <option value="active">Active</option>
          <option value="in-active">In-Active</option>
        </select>
        <label class="mt-4 block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null">All</option>
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <action-add v-if="authorize.create" :href="route('admin.testimonials.create')" title="testimonial" />
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-6">Sr. No.</th>
            <Sortable v-model="form.sort" class="py-3 px-6" label="Title" column="title" :sort="form.sort" />
            <th class="py-3 px-6">Author</th>
            <th class="py-3 px-6">Status</th>
            <Sortable v-model="form.sort" class="py-3 px-6" label="Date" column="created_at" :sort="form.sort" />
            <th class="py-3 px-6 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="testimonial in testimonials.data" :key="testimonial.id" class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ testimonial.srno }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ testimonial.title }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <div class="flex items-center">
                <div v-if="testimonial.photo" class="flex-shrink-0 h-10 w-10">
                  <img class="h-10 w-10 rounded-full" :src="testimonial.photo" />
                </div>
                <div :class="testimonial.photo ? 'ml-4' : ''">
                  <div class="text-sm font-medium text-gray-900">
                    {{ testimonial.name }}
                  </div>
                </div>
              </div>
            </td>
            <td class="py-3 px-6">
              <toggle-status :status="testimonial.is_active" @toggleStatus="toggleStatus(testimonial)" />
            </td>
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ testimonial.created_at }}</span>
            </td>
            <td class="py-3 px-6">
              <div class="flex item-center justify-center">
                <action-preview v-if="authorize.view" :href="route('admin.testimonials.show', testimonial.id)" title="View" />
                <action-edit v-if="authorize.update" :href="route('admin.testimonials.edit', testimonial.id)" title="Edit" />
                <action-delete v-if="!testimonial.deleted_at && authorize.delete" title="Delete" @delete="destroy(testimonial)" />
              </div>
            </td>
          </tr>
          <tr v-if="testimonials.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No testimonial's found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="testimonials.links" />
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
  metaInfo: { title: 'testimonial\'s' },
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
    testimonials: Object,
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
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('admin.testimonials.index', query))
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
      if(!vInstance.authorize.delete) return
      swal('Are you sure you want to delete this testimonials?', {
        buttons: ['No', 'Yes'],
      }).then(value => {
        if (value) {
          vInstance.$inertia.delete(vInstance.route('admin.testimonials.destroy', item.id))
        }
      })
    },
    toggleStatus(item) {
      const vInstance = this
      if(!vInstance.authorize.update) return
      swal(`Are you sure you want to ${item.is_active ? 'de-activate' : 'activate'} this testimonial?`, {
        buttons: ['No', 'Yes'],
      }).then(value => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.testimonials.toggle.status', item.id))
        }
      })
    },
  },
}
</script>
