<template>
  <inertia-link class="btn-indigo" :href="href">
    <span>Add</span>
    <span class="hidden md:inline">{{ title }}</span>
  </inertia-link>
</template>

<script>
export default {
  props: {
    href: String,
    title: String,
  },
}
</script>
