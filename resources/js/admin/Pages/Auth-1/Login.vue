<template>
  <div class="p-6 bg-indigo-800 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <!-- <logo class="block mx-auto w-full max-w-xs fill-white" height="50" /> -->
      <flash-messages />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h1 class="text-center">
            <logo class="life-logo" />
          </h1>
          <div class="mx-auto mt-5 w-24 border-b-2" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-5" label="Email" type="email" :required="true" maxlength="100" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-2" label="Password" type="password" :required="true" autofocus autocapitalize="off" />
          <label class="mt-2 select-none flex items-center" for="remember">
            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
            <span class="text-sm">Remember Me</span>
          </label>
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center">
          <inertia-link :href="route('admin.password.request')" class="hover:underline" tabindex="-1"> Forgot Password? </inertia-link>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Login</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
//import Logo from '@admin/Shared/Logo';
import TextInput from '@admin/Shared/TextInput'
import LoadingButton from '@admin/Shared/LoadingButton'
import FlashMessages from '@admin/Shared/FlashMessages'
import Logo from '@admin/Shared/Logo'

export default {
  metaInfo: { title: 'Login' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
    FlashMessages,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form
        .transform((data) => ({
          ...data,
          remember: data.remember ? 'on' : '',
        }))
        .post(this.route('admin.login.attempt'))
    },
  },
}
</script>

<style scoped>
.life-logo {
  max-height: 70px;
  margin-left: 115px;
  margin-bottom: 20px;
}
</style>
