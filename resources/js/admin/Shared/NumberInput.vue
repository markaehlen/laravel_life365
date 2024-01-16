<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required" class="text-red-500 text-xs italic">*</span></label>
    <input :required="required" :id="id" :max="max" :step="step" ref="input" v-bind="$attrs" class="form-input focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-transparent" :class="{ error: error }" type="number" :min="min" :value="value" @input="$emit('input', $event.target.value)" />
    <small class="font-semibold text-muted">{{ help }}</small>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: [Number, String],
    min: Number,
    max: Number,
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
  },
}
</script>
