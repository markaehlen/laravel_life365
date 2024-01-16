<template>
  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-green-800 cursor-pointer" :class="{ 'bg-green-100': status, 'bg-red-100': !status}" @click="$emit('toggleStatus')">
    {{ status ? 'Active' : 'Inactive' }}
  </span>
</template>
<script>
export default {
  props:{
    status: Boolean,
  },
}
</script>
