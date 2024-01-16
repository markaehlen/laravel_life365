<template>
  <div id="users">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Add Testimonial</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none inline-flex">
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
              <a href="javascript:void" class="text-gray-600">Add</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white overflow-hidden">
      <form @submit.prevent="store">
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
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import TextInput from '@admin/Shared/TextInput'
import FileInput from '@admin/Shared/FileInput'
import ActionBack from '@admin/Shared/ActionBack'
import TextareaInput from '@admin/Shared/TextareaInput'
import LoadingButton from '@admin/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Add testimonial\'s' },
  components: {
    Icon,
    FileInput,
    TextInput,
    ActionBack,
    TextareaInput,
    LoadingButton,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: null,
        name: null,
        description: null,
        photo: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('admin.testimonials.store'))
    },
  },
}
</script>
