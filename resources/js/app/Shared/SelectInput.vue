<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required" class="text-red-500 text-xs italic">*</span></label>
    <ValidationProvider class="inputBlock block" :name="label.toLowerCase()" :rules="rules" v-slot="{ errors }">
      <select @mouseover="showInfo(toolTipText)" @mouseleave="removeInfo()" :required="required" :id="id" ref="input" v-model="selected" v-bind="$attrs" class="form-select focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent" :class="{ error: error }" @blur="$emit('blur', $event.target.value)">
        <slot />
      </select>
      <ul>
        <li v-for="error in errors" :key="error" class="font-semibold error_class">{{ error }}</li>
      </ul>
    </ValidationProvider>
    <div v-if="error" class="form-error font-semibold">{{ error }}</div>
  </div>
</template>

<script>
import { extend } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import * as messages from 'vee-validate/dist/locale/en.json'
import Info from '@app/Shared/Info'
import Icon from '@app/Shared/Icon'

Object.keys(rules).forEach((rule) => {
  extend(rule, {
    ...rules[rule], // copies rule configuration
    message: messages[rule], // assign message
  })
})
export default {
  components: { Info, Icon },
  inheritAttrs: false,
  props: {
    toolTipText: {
      type: String,
      default: '',
    },
    rules: {
      type: String,
      default: '',
    },
    id: {
      type: String,
      default() {
        return `select-input-${this._uid}`
      },
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: [String, Number, Boolean],
    label: String,
    error: String,
  },
  data() {
    return {
      selected: this.value,
    }
  },
  watch: {
    value: function () {
      this.selected = this.value
    },
    selected(selected) {
      this.$emit('input', selected)
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
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
}
.vue-custom-tooltip.is-right:after,
.vue-custom-tooltip.is-right:before {
  padding: 15px !important;
}
.vue-custom-tooltip:after {
  border-radius: 6px;
  font-size: 10px !important;
}
</style>
