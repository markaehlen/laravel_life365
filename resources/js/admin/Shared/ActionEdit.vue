<template>
  <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
    <inertia-link :href="href" :title="title">
      <icon name="edit" class="text-indigo-400" />
    </inertia-link>
  </div>
</template>

<script>
import Icon from '@admin/Shared/Icon'

export default {
  components: {
    Icon,
  },
  props: {
    href: String,
    title: {
      type: String,
      default() {
        return 'Edit'
      },
    },
  },
}
</script>
