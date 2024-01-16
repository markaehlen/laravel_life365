<template>
  <div class="p-6 flex justify-center items-center">
    <div class="w-full max-w-md">
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="register">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-2xl">Register</h1>
          <div class="mx-auto mt-5 w-24 border-b-2" />
          <text-input v-model="form.first_name" :required="true" :error="form.errors.first_name" class="mt-5" label="First Name" type="text" autofocus autocapitalize="on" />
          <text-input v-model="form.last_name" :required="true" :error="form.errors.last_name" class="mt-5" label="Last Name" type="text" autofocus autocapitalize="on" />
          <text-input v-model="form.email" :required="true" :error="form.errors.email" class="mt-5" label="Email" type="email" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :required="true" :error="form.errors.password" class="mt-2" label="Password" type="password" autofocus autocapitalize="off" />
          <text-input v-model="form.password_confirmation" :required="true" :error="form.errors.password_confirmation" class="mt-2" label="Confirm Password" type="password" autofocus autocapitalize="off" />
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center">
          <span>Already have account? <inertia-link :href="route('login')" class="hover:underline" tabindex="-1"> Login</inertia-link></span>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Register</loading-button>
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
  metaInfo: { title: 'Register' },
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
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
      }),
    }
  },
  methods: {
    register() {
      this.form
        .transform((data) => ({
          ...data,
        }))
        .post(this.route('register'))
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
