<template>
  <div id="faqs">
    <trashed-message v-if="testimonial.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore">
      This testimonial has been deleted.
    </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Testimonial</h1>
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
          <text-input v-model="form.title" :error="form.errors.title" :required="true" class="pr-5 pb-2 w-full lg:w-3/5" label="Title" />
          <text-input v-model="form.name" :error="form.errors.name" :required="true" class="pr-5 pb-2 w-full lg:w-3/5" label="Name" />
          <textarea-input v-model="form.description" :error="form.errors.description" :required="true" rows="8" class="pr-5 pb-2 w-full lg:w-3/5" label="Description" />
          <file-input v-model="form.photo" :error="form.errors.photo" class="pr-5 pb-2 w-full lg:w-3/5" type="file" accept="image/*" label="Photo" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.testimonials.index')" />
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
import FileInput from '@admin/Shared/FileInput'
import ActionBack from '@admin/Shared/ActionBack'
import TextareaInput from '@admin/Shared/TextareaInput'
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
    FileInput,
    TextInput,
    ActionBack,
    TextareaInput,
    LoadingButton,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    testimonial: Object,
    authorize: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        id: this.testimonial.id,
        title: this.testimonial.title,
        name: this.testimonial.name,
        description: this.testimonial.description,
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.testimonials.update', this.testimonial.id), {
        onSuccess: () => this.form.reset(),
      })
    },
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
