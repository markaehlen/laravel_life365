<template>
  <div class="w-full border-t-2 bg-gray-100 px-5 py-5 block 2xl:flex xl:flex lg:flex md:flex footer-block-md flex-nowrap justify-between items-center">
    <p class="mb-2 lg:mb-0">© Copyright Life-365™ {{ year }}</p>
    <div class="flex ft-links-md">
      <a href="javascript:void" class="mr-6 hover:text-gray-900">Version: 1.0</a>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Footer',
  data() {
    return {
      year: new Date().getFullYear(),
    }
  },
}
</script>
