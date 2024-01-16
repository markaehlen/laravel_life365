<template>
  <div>
    <div class="p-5 -mr-5 -mb-2 flex flex-wrap">
      <div class="w-full border-b flex flex-nowrap items-center text-center bg-gray-50 px-5 mb-5 mr-5">
        <h5 class="pr-5 pb-2 w-full font-semibold">Uncertainty Settings</h5>
      </div>
      <ValidationObserver ref="form" class="w-full">
        <form class="flex flex-wrap w-full" @submit.prevent="store">
          <div v-if="form.uncertainty" class="w-full border-b flex flex-nowrap items-center bg-gray-50 px-5 mb-5 mr-5">
            <input :checked="form.uncertainty.useDefaults" class="p-2" v-model="form.uncertainty.useDefaults" type="checkbox" />
            <label class="p-4 font-semibold">Use default values</label>
          </div>
          <number-input :disabled="form.uncertainty.useDefaults" :min="0" :max="100" v-model="form.uncertainty.coVd28" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.coVd28" :required="true" class="pr-5 pb-2 w-full" :label="'COV of D28 (%)'" />
          <number-input :disabled="form.uncertainty.useDefaults" :min="0" :max="100" v-model="form.uncertainty.coVm" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.coVm" :required="true" class="pr-5 pb-2 w-full" label="COV of m (%)" />
          <number-input :disabled="form.uncertainty.useDefaults" :min="0" :max="100" v-model="form.uncertainty.covCs" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.covCs" :required="true" class="pr-5 pb-2 w-full" label="COV of Cs (%)" />
          <number-input :disabled="form.uncertainty.useDefaults" :min="0" :max="100" v-model="form.uncertainty.covCt" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.covCt" :required="true" class="pr-5 pb-2 w-full" label="COV of Ct (%)" />
          <number-input :disabled="form.uncertainty.useDefaults" :min="0" :max="100" v-model="form.uncertainty.covCover" :rules="`required|min_value:0|max_value:100`" :title="`Allowable range of values is [0 , 100]`" step="any" :error="form.errors.covCover" :required="true" class="pr-5 pb-2 w-full" label="COV of cover (%)" />
          <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 mb-5 mr-5">
            <loading-button :loading="form.processing" class="btn-indigo-flat mr-5" style="cursor: pointer" type="submit">Save</loading-button>
            <span class="btn-indigo-flat" style="cursor: pointer" @click="$emit('close', form.uncertainty)">Close</span>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </div>  
</template>

<script>
import Icon from '@app/Shared/Icon'
import TextInput from '@app/Shared/TextInput'
import NumberInput from '@app/Shared/NumberInput'
import ActionBack from '@app/Shared/ActionBack'
import TextareaInput from '@app/Shared/TextareaInput'
import LoadingButton from '@app/Shared/LoadingButton'
import SelectInput from '@app/Shared/SelectInput.vue'
import AltsList from '@app/Pages/Project/AltsList.vue'
import VSwatches from 'vue-swatches'
import 'vue-swatches/dist/vue-swatches.css'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import InlineTextEditor from '@app/Shared/InlineTextEditor'

export default {
  metaInfo: { title: 'UP' },
  components: {
    Icon,
    pickBy,
    throttle,
    TextInput,
    NumberInput,
    SelectInput,
    ActionBack,
    LoadingButton,
    TextareaInput,
    VSwatches,
    AltsList,
    InlineTextEditor,
  },
  props: {
    uncertainty: [Object, Array],
  },
  data() {
    return {
      form: this.$inertia.form({
        uncertainty: this.uncertainty,
      }),
    }
  },
  mounted() {},
  methods: {
    store() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          window.scrollTo(0, 0)
          return
        }
        this.form.processing = true
        this.updateUncertainty(this.form.uncertainty)
      })
    },
    updateUncertainty: async function (uncertainty) {
      await axios
        .post('/update-uncertainty', {
          uncertainty: this.form.uncertainty,
        })
        .then(function (response) {
          const { data } = response
          console.log('Uncertainty Updated.')
        })
        .catch(function (error) {
          console.log(error)
        })
    },
  },
}
</script>
