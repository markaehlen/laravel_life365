<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required" class="text-red-500 text-xs italic">*</span></label>
    <ckeditor :id="id" ref="input" :editor="editor" v-bind="$attrs" class="form-editor" :class="{ error: error }" :value="value" :config="editorConfig" @input="input" />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import CKEditor from '@ckeditor/ckeditor5-vue2'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

export default {
  components: {
    ckeditor: CKEditor.component,
  },
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `editor-input-${this._uid}`
      },
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: String,
    label: String,
    error: String,
  },
  data() {
    return {
      editor: ClassicEditor,
      editorConfig: {
        //The configuration of the editor.
      },
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    input(value) {
      this.$emit('input', value)
    },
  },
}
</script>
