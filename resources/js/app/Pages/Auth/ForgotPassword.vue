<template>
  <div class="p-6 flex justify-center items-center login-block">
    <div class="w-full max-w-md">
      <!-- <logo class="block mx-auto w-full max-w-xs fill-white" height="50" /> -->
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="sendResetPasswordEmail">
        <div class="px-10 py-12 inst-login-block">
          <h1 class="text-center font-bold text-2xl">Forgot Password</h1>
          <div class="mx-auto mt-5 w-24 border-b-2" />
          <!-- <text-input :required="true" v-model="form.email" :error="form.errors.email" class="mt-5" label="Email" autofocus autocapitalize="off" /> -->
          <text-input 
                :required="true" 
                v-model="form.email" 
                :class="{'input-error': form.errors.email}" 
                class="mt-5" 
                label="Email" 
                
                autofocus 
                autocapitalize="off" 
            />

        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center inst-login-block inl-blck">
          <inertia-link class="hover:underline" :href="route('login')">Login</inertia-link>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Send Password Reset Email</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import TextInput from '@app/Shared/TextInput'
import LoadingButton from '@app/Shared/LoadingButton'
import FlashMessages from '@app/Shared/FlashMessages'
import Layout from '@app/Shared/Layout'

export default {
  metaInfo: { title: 'Forgot Password' },
  components: {
    LoadingButton,
    //Logo,
    TextInput,
    FlashMessages,
  },
  layout: Layout,
  data() {
    return {
      form: this.$inertia.form({
        email: '',
      }),
    }
  },
  methods: {
    sendResetPasswordEmail() {
      this.form
        .transform((data) => ({
          ...data,
        }))
        .post(this.route('password.email'), {
          onSuccess: () => window.location.reload(),
        })
    },
  },
}
</script>
<style>
.input-error {
  border-color: red;
  /* Add more styles if needed */
}
</style>
