<template>
  <div id="pages">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Edit Help Tips</h1>
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
              <inertia-link :href="route('admin.help-tips.index')" class="text-gray-700">
                Help Tips
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
          <text-input v-model="form.title" :error="form.errors.title" class="pr-5 pb-2 w-full" label="Title" />
          <text-input v-model="form.category" :error="form.errors.category" :readonly="true" class="pr-5 pb-2 w-full" label="Tab" />
          <TextareaInput v-model="form.content" :error="form.errors.content" class="pr-5 pb-2 w-full" label="Content" />          
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.help-tips.index')" />
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
        category: this.page.category,
        content: this.page.content,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(this.route('admin.help-tips.update', this.page.id), {
        onSuccess: () => this.form.reset(),
      })
    },
  },
}
</script>
