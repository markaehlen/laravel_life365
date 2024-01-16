<!-- This is a vue used by a number of other vues; not sure what it's for though... -->
<template>
  <div class="w-full p-4 bg-yellow-300 rounded flex items-center justify-between">
    <div class="flex items-center">
      <icon name="trash" class="flex-shrink-0 w-4 h-4 fill-yellow-800 mr-2" />
      <div class="text-sm font-medium text-yellow-800">
        <slot />
      </div>
    </div>
    <button v-if="canRestore" class="text-sm text-yellow-800 hover:underline" tabindex="-1" type="button"
      @click="$emit('restore')">Restore</button>
  </div>
</template>

// procedural code
<script>

// use this Icon view
import Icon from '@admin/Shared/Icon'

// constructor  
export default {

  // Just an ICON?
  components: {
    Icon,
  },
  props: {
    canRestore: Boolean,
  },
}
</script>
