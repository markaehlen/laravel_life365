<template>
  <div id="helpwindows">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Help Window</h1>
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
              <inertia-link :href="route('admin.helpwindows.index')" class="text-gray-700">
                Help Windows
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
          <text-input v-model="form.title" :error="form.errors.title" :required="true" class="pr-5 pb-2 w-full" label="Title" />
          <textarea-input v-model="form.excerpt" :error="form.errors.excerpt" :required="true" class="pr-5 pb-2 w-full" label="Short Description" />
          <tinymce-input v-model="form.content" :error="form.errors.content" :required="true" class="pr-5 pb-2 w-full" label="Content" />
          <text-input v-model="form.meta_title" :error="form.errors.meta_title" class="pr-5 pb-2 w-full" label="Meta title" />
          <textarea-input v-model="form.meta_keyword" :error="form.errors.meta_keyword" class="pr-5 pb-2 w-full" label="Meta keywords" />
          <textarea-input v-model="form.meta_description" :error="form.errors.meta_description" class="pr-5 pb-2 w-full" label="Meta description" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.helpwindows.index')" />
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
import ActionBack from '@admin/Shared/ActionBack'
import TinymceInput from '@admin/Shared/TinymceInput'
import TextareaInput from '@admin/Shared/TextareaInput'
import LoadingButton from '@admin/Shared/LoadingButton'

export default {
  metaInfo() {
    return {
      title: this.form.title,
    }
  },
  components: {
    Icon,
    TextInput,
    ActionBack,
    TinymceInput,
    TextareaInput,
    LoadingButton,
  },
  layout: Layout,
  props: {
    page: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        id: this.page.id,
        title: this.page.title,
        meta_title: this.page.meta_title,
        meta_keyword: this.page.meta_keyword,
        meta_description: this.page.meta_description,
        excerpt: this.page.excerpt,
        content: this.page.content,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.helpwindows.update', this.page.id), {
        onSuccess: () => this.form.reset(),
      })
    },
  },
}
</script>
