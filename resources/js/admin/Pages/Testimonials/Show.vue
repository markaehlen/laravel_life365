<template>
  <div id="preview">
    <trashed-message v-if="testimonial.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore">
      This testimonial has been deleted.
    </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Testimonial's</h1>
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
              <inertia-link :href="route('admin.testimonials.index')" class="text-gray-700">
                Testimonial's
              </inertia-link>
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
            <td class="font-semibold text-sm leading-normal py-3 px-6">Title</td>
            <td class="py-3 px-6">
              <span>{{ testimonial.title }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Name</td>
            <td class="py-3 px-6">
              <span>{{ testimonial.name }}</span>
            </td>
          </tr>

          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Description</td>
            <td class="py-3 px-6">
              <span>{{ testimonial.description }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Photo</td>
            <td class="py-3 px-6">
              <div v-if="testimonial.photo" class="flex-shrink-0 h-10 w-10">
                <img class="h-10 w-10 rounded-full" :src="testimonial.photo" />
              </div>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Created At</td>
            <td class="py-3 px-6">
              <span>{{ testimonial.created_at }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex  items-center">
      <action-back :href="route('admin.testimonials.index')" />
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
  name: 'FaqPreview',
  metaInfo() {
    return {
      title: this.testimonial.title,
    }
  },
  components: {
    Icon,
    ActionBack,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    testimonial: Object,
    authorize: Object,
  },
  methods: {
    restore() {
      const vInstance = this
      if(!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this testimonial?', {
        buttons: ['No', 'Yes'],
      }).then(value => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.testimonials.restore', vInstance.testimonial.id))
        }
      })
    },
  },
}
</script>
