<!-- A template for all text inputs; this vue is used in ALOT of places -->
<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required"
        class="text-red-500 text-xs italic">*</span></label>
    <input :required="required" :id="id" ref="input" v-bind="$attrs"
      class="form-input focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent"
      :class="{ error: error }" :type="type" :value="value" @input="$emit('input', $event.target.value)" />
    <small class="font-semibold text-muted">{{ help }}</small>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>

// create the widget
export default {
  inheritAttrs: false,
  props: {
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
  },
  methods: {

    // to 'focus' on this view is to focus on the 'input' defined in the template above
    focus() {
      this.$refs.input.focus()
    },

    // to 'select' this vue is to select the 'input' defined in the template above
    select() {
      this.$refs.input.select()
    },

    // set the min and max allowable
    setSelectionRange(start, end) {

      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
