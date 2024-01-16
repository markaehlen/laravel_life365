<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required" class="text-red-500 text-xs italic">*</span></label>
    <ValidationProvider class="inputBlock block" :name="label.toLowerCase()" :rules="rules" v-slot="{ errors }">
      <input @mouseover="showInfo(toolTipText)" @mouseleave="removeInfo()" :required="required" :id="id" :max="max" :step="step" ref="input" v-bind="$attrs" class="form-input focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent" :class="{ error: error }" type="number" :min="min" :value="value" @input="$emit('input', $event.target.value)" @blur="$emit('blur', $event.target.value)" />
      <ul>
        <li v-for="error in errors" :key="error" class="font-semibold error_class">{{ error }}</li>
      </ul>
    </ValidationProvider>
    <small class="font-semibold text-muted">{{ help }}</small>
    <div v-if="error" class="form-error font-semibold">{{ error }}</div>
  </div>
</template>

<script>
import { extend } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import Info from '@app/Shared/Info'
import * as messages from 'vee-validate/dist/locale/en.json'

Object.keys(rules).forEach((rule) => {
  extend(rule, {
    ...rules[rule], // copies rule configuration
    message: messages[rule], // assign message
  })
})
export default {
  components: {
    // VueCustomTooltip,
  },
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    toolTipText: {
      type: String,
      default: '',
    },
    rules: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: [Number, String],
    min: [Number, String],
    max: [Number, String],
    step: String,
    help: String,
    label: String,
    error: String,
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
    showInfo(msg) {
      this.$store.state.tipMessage = msg
    },
    removeInfo() {
      this.$store.state.tipMessage = 'Tips/help for specific fields will be displayed here.'
    },
  },
}
</script>
<style scoped>
.error_class {
  color: #d80000;
  font-size: 12 !important;
}
.vue-custom-tooltip.is-right:after,
.vue-custom-tooltip.is-right:before {
  padding: 15px !important;
}
.vue-custom-tooltip:after {
  border-radius: 6px;
}
</style>
