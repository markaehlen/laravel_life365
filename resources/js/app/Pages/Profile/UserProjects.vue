<template>
  <div id="projects">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">My Projects</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">My Projects</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="mb-5 flex justify-between bg-gray-50 py-5 px-5 items-center border-b">
      <search-box v-model="form.search" class="w-full max-w-md mr-4" @reset="reset"> </search-box>
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-6">#</th>
            <th class="py-3 px-6">Edit</th>
            <Sortable v-model="form.sort" class="py-3 px-6" label="Title" column="title" :sort="form.sort" />
            
            <th class="py-3 px-6">Description</th>
            <Sortable v-model="form.sort" class="py-3 px-6" label="Date" column="created_at" :sort="form.sort" />
            <th class="py-3 px-6 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="project in projects.data" :key="project.id" class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ project.srno }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <action-edit :href="route('open-saved-project', project.unique_id)" title="Open Project" />
            </td> 
            <td class="py-3 px-6 whitespace-nowrap">  
                <span class="text-sm font-medium text-gray-900">{{ project.title }}</span>
            </td>
             
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ project.description }}</span>
            </td>
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ format_date(project.date)}}</span>
            </td>
            <td class="py-3 px-6">
              <div class="flex item-center justify-center">
                <!-- <action-preview :href="route('my-projects.show', project.id)" title="View" /> -->
                <!-- <action-edit :href="route('open-saved-project', project.unique_id)" title="Open Project" /> -->
                <action-delete title="Delete" @delete="destroy(project)" />
              </div>
            </td>
          </tr>
          <tr v-if="projects.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No project's found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="projects.links" />
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert'
import pickBy from 'lodash/pickBy'
import Icon from '@app/Shared/Icon'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Layout from '@app/Shared/Layout'
import Sortable from '@app/Shared/Sortable'
import ActionAdd from '@app/Shared/ActionAdd'
import ActionEdit from '@app/Shared/ActionEdit'
import Pagination from '@app/Shared/Pagination'
import ActionDelete from '@app/Shared/ActionDelete'
import SearchBox from '@admin/Shared/SearchBox'
import ActionPreview from '@app/Shared/ActionPreview'
import ToggleStatus from '@app/Shared/ToggleStatus.vue'
import moment from 'moment'

export default {
  metaInfo: { title: "testimonial's" },
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
    projects: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        sort: this.filters.sort,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.get(this.route('my-projects.index', query))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    format_date(value){
         if (value) {
           return moment(String(value)).format('DD/MM/YYYY')
          }
      },
    destroy(item) {
      const vInstance = this
      swal('Are you sure you want to delete this project?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.delete(vInstance.route('my-projects.destroy', item.id))
        }
      })
    },
  },
}
</script>
