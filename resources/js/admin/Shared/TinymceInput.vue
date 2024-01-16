<!-- A form and actions to support a text editor box -->
<template>
  <div>
    <label v-if="label" class="form-label text-sm font-semibold" :for="id">{{ label }} <span v-if="required"
        class="text-red-500 text-xs italic">*</span></label>
    <editor :id="id" ref="input" api-key="no-api-key" :init="config" v-bind="$attrs" class="form-editor"
      :class="{ error: error }" :value="value" @input="input" />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

// procedural code
<script>
// import the detailed editor from tinymce
import Editor from '@tinymce/tinymce-vue'

// create the widget
export default {
  name: 'TinymceInput',
  components: {
    editor: Editor,
  },
  inheritAttrs: false,
  props: {

    // the id of this vue is the id of the input control
    id: {
      type: String,
      default() {
        return `tinymce-input-${this._uid}`
      },
    },

    // By default no input is required
    required: {
      type: Boolean,
      default: false,
    },
    value: String,
    label: String,
    error: String,
  },

  // when data is asked of it, it returns this configuration
  data() {
    return {
      config: {
        height: 400,
        menubar: false,
        plugins: ['advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table paste code help wordcount'],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image fullpage | forecolor backcolor emoticons | preview | code',
        fullpage_default_encoding: 'UTF-8',
        fullpage_default_doctype: '<!DOCTYPE html>',
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
