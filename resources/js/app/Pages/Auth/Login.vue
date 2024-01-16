<template> 
  <div class="p-6 flex justify-center items-center login-block"> 
    <div class="w-full max-w-md">
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12 inst-login-block">
          <h1 class="text-center font-bold text-2xl">Login</h1>
          <div class="mx-auto mt-5 w-24 border-b-2" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-5" label="Email" type="email" :required="true" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-2" label="Password" type="password" :required="true" autofocus autocapitalize="off" />
          <label class="mt-2 select-none flex items-center" for="remember">
            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
            <span class="text-sm">Remember Me</span>
          </label>
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center inst-login-block">
          <span>New to Life-365™? <inertia-link :href="route('register')" class="hover:underline" tabindex="-1"> Register Now</inertia-link></span>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Login</loading-button>
        </div>
        <div class="px-10 register-option bg-gray-100 border-t border-gray-100 flex justify-between items-center inst-login-block">
          <inertia-link :href="route('password.request')" class="hover:underline" tabindex="-1"> Forgot Password? </inertia-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import TextInput from '@app/Shared/TextInput'
import LoadingButton from '@app/Shared/LoadingButton'
import FlashMessages from '@app/Shared/FlashMessages'
import Logo from '@app/Shared/Logo'
import Layout from '@app/Shared/Layout'

export default {
  metaInfo: { title: 'Login' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
    FlashMessages,
  },
  layout: Layout,
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
        .post(this.route('login.attempt'))
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

.register-option {
  padding-top: 0rem !important;
  padding-bottom: 1rem !important;
}
</style>
