<template>
  <inertia-link :href="href" class="text-red-600 hover:underline" type="button">
    <slot>Back</slot>
  </inertia-link>
</template>
<script>
export default {
  props: {
    href: String,
  },
}
</script>
