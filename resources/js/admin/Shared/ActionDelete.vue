<template>
  <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" :title="title" @click="$emit('delete')">
    <icon name="delete" class="text-indigo-400" />
  </div>
</template>

<script>
import Icon from '@admin/Shared/Icon'

export default {
  components: {
    Icon,
  },
  props: {
    title: {
      type: String,
      default() {
        return 'Delete'
      },
    },
  },
}
</script>
