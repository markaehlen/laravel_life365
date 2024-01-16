<template>
  <div id="email-templates">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Manage Email Template</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Email Templates</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <!-- <th class="py-3 px-6">Sr. No.</th> -->
            <Sortable v-model="form.sort" class="py-3 px-6" label="Name" column="name" :sort="form.sort" />
            <Sortable v-model="form.sort" class="py-3 px-6" label="Subject" column="subject" :sort="form.sort" />
            <!-- <Sortable v-model="form.sort" class="py-3 px-6" label="Date" column="created_at" :sort="form.sort" /> -->
            <th class="py-3 px-6 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="emailTemplate in emailTemplates.data" :key="emailTemplate.id" class="border-b border-gray-200 hover:bg-gray-100">
            <!-- <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ emailTemplate.srno }}</span>
            </td> -->
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ emailTemplate.name }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ emailTemplate.subject }}</span>
            </td>
            <!-- <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ emailTemplate.created_at }}</span>
            </td> -->
            <td class="py-3 px-6">
              <div class="flex item-center justify-center">
                <div v-if="authorize.view" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                  <a :href="route('admin.email-templates.show', emailTemplate.id)" title="View">
                    <icon name="eye" class="text-indigo-400" />
                  </a>
                </div>
                <action-edit v-if="authorize.update" :href="route('admin.email-templates.edit', emailTemplate.id)" title="Edit" />
              </div>
            </td>
          </tr>
          <tr v-if="emailTemplates.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No email templates found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="emailTemplates.links" />
    </div>
  </div>
</template>

<script>
import pickBy from 'lodash/pickBy'
import Icon from '@admin/Shared/Icon'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Layout from '@admin/Shared/Layout'
import Sortable from '@admin/Shared/Sortable'
import ActionEdit from '@admin/Shared/ActionEdit'
import Pagination from '@admin/Shared/Pagination'
import SearchFilter from '@admin/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Email Template' },
  components: {
    Icon,
    Sortable,
    ActionEdit,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    emailTemplates: Object,
    filters: Object,
    authorize: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        sort: this.filters.sort,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('admin.email-templates.index', query))
      }, 150),
      deep: true,
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
