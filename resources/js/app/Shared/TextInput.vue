<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required" class="text-red-500 text-xs italic">*</span></label>
    <ValidationProvider class="block" :name="label.toLowerCase()" :rules="rules" v-slot="{ errors }">
      <input @mouseover="showInfo(toolTipText)" @mouseleave="removeInfo()" :required="required" :id="id" ref="input" v-bind="$attrs" class="form-input focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent" :class="{ error: error }" :type="type" :value="value" @input="$emit('input', $event.target.value)" />
      <ul>
        <li v-for="error in errors" :key="error" class="error_class font-semibold">{{ error }}</li>
      </ul>
    </ValidationProvider>
    <small class="font-semibold text-muted">{{ help }}</small>
    <div v-if="error" class="form-error font-semibold">{{ error }}</div>
  </div>
</template>

<script>
import { extend } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import * as messages from 'vee-validate/dist/locale/en.json'
import Info from '@app/Shared/Info'

Object.keys(rules).forEach((rule) => {
  extend(rule, {
    ...rules[rule], // copies rule configuration
    message: messages[rule], // assign message
  })
})

export default {
  inheritAttrs: false,
  props: {
    rules: {
      type: String,
      default: '',
    },
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: String,
    help: String,
    label: String,
    error: String,
    toolTipText: {
      type: String,
      default: '',
    },
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
</style>
