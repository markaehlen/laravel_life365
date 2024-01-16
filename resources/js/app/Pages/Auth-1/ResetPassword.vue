<template>
  <div class="p-6 flex justify-center items-center">
    <div class="w-full max-w-md">
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="resetPassword">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-2xl">Reset Password</h1>
          <div class="mx-auto mt-5 w-24 border-b-2" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-2" label="Email" type="text" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-2" label="Password" type="password" autofocus autocapitalize="off" />
          <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="mt-2" label="Confirm Password" type="password" />
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Reset Password</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
//import Logo from '@admin/Shared/Logo';
import TextInput from '@app/Shared/TextInput'
import LoadingButton from '@app/Shared/LoadingButton'
import FlashMessages from '@app/Shared/FlashMessages'
import Layout from '@app/Shared/Layout'

export default {
  metaInfo: { title: 'Reset Password' },
  components: {
    LoadingButton,
    //Logo,
    TextInput,
    FlashMessages,
  },
  layout: Layout,
  props: {
    token: String,
  },
  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: '',
        password: '',
        password_confirmation: false,
      }),
    }
  },
  methods: {
    resetPassword() {
      this.form
        .transform((data) => ({
          ...data,
        }))
        .post(this.route('password.reset.attempt'))
    },
  },
}
</script>
