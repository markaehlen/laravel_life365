<template>
  <div id="tips">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Help Tips</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Help Tips</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="mb-5 block 2xl:flex xl:flex lg:flex md:flex justify-between bg-gray-50 py-5 px-5 items-center border-b">
      <search-box v-model="form.search" class="w-full max-w-md mr-4" @reset="reset"> </search-box>
      <!-- <action-add class="add-btn-mobile" :href="route('admin.help-tips.create')" title="Tips" /> -->
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full whitespace-nowrap text-left">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <Sortable v-model="form.sort" class="py-3 px-6" label="Label Title" column="title" :sort="form.sort" />
            <Sortable v-model="form.sort" class="py-3 px-6" label="Tab" column="category" :sort="form.sort" />
            <Sortable v-model="form.sort" class="py-3 px-6" label="Slug" column="slug" :sort="form.sort" />
            <Sortable v-model="form.sort" class="py-3 px-6" label="Date" column="created_at" :sort="form.sort" />
            <th class="py-3 px-6 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="tip in tips.data" :key="tip.id" class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ tip.title }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ tip.category }}</span>
            </td>
            <td class="py-3 px-6 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ tip.slug }}</span>
            </td>
            <td class="py-3 px-6">
              <span class="text-sm font-medium text-gray-900">{{ tip.created_at }}</span>
            </td>
            <td class="py-3 px-6">
              <div class="flex item-center justify-center">
                <action-preview :href="route('admin.help-tips.show', tip.id)" title="View" />
                <action-edit :href="route('admin.help-tips.edit', tip.id)" title="Edit" />                
              </div>
            </td>
          </tr>
          <tr v-if="tips.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4"><b>No tips found.</b></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <pagination :links="tips.links" />
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
import SearchBox from '@admin/Shared/SearchBox'
import ActionPreview from '@admin/Shared/ActionPreview'
import ActionAdd from '@admin/Shared/ActionAdd'

export default {
  metaInfo: { title: 'Help Tips' },
  components: {
    Icon,
    Sortable,
    Pagination,
    ActionEdit,
    SearchBox,
    ActionPreview,
    ActionAdd,
  },
  layout: Layout,
  props: {
    tips: Object,
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
        this.$inertia.replace(this.route('admin.help-tips.index', query))
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
