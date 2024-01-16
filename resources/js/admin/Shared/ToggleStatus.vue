<!-- IS THIS EVEN USED? Layout and constructor for a status bar -->
<template>
  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-green-800 cursor-pointer"
    :class="{ 'bg-green-100': status, 'bg-red-100': !status }" @click="$emit('toggleStatus')">
    {{ status ? 'Active' : 'Inactive' }}
  </span>
</template>

// procedural code
<script>

// create the control
export default {

  // define the properties
  props: {
    status: Boolean,
  },
}
</script>
