<template>
  <div id="users">
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Add User</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.users.index')" class="text-gray-700"> Users </inertia-link>
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
          <text-input v-model="form.first_name" maxlength="50" :error="form.errors.first_name" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="First name" />
          <text-input v-model="form.last_name" maxlength="50" :error="form.errors.last_name" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="Last name" />
          <text-input v-model="form.email" maxlength="50" :error="form.errors.email" :required="true" class="pr-5 pb-2 w-full lg:w-1/3" label="Email" type="email"/>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <action-back :href="route('admin.users.index')" />
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import FileInput from '@admin/Shared/FileInput'
import TextInput from '@admin/Shared/TextInput'
import ActionBack from '@admin/Shared/ActionBack'
import SelectInput from '@admin/Shared/SelectInput'
import LoadingButton from '@admin/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Add User' },
  components: {
    Icon,
    TextInput,
    FileInput,
    ActionBack,
    SelectInput,
    LoadingButton,
  },
  layout: Layout,
  props: {},
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        first_name: null,
        last_name: null,
        email: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('admin.users.store'))
    },
  },
}
</script>
