<template>
  <div id="astm">
    <div class="bg-white overflow-hidden"></div>
    <div class="w-full border-b flex flex-nowrap items-center bg-gray-50 p-2">
      <h3 class="font-semibold"><v-icon class="icon" name="info-circle"/>  {{ title }}</h3>
    </div>
    <div class="w-full border-b flex flex-nowrap items-center p-2">{{ content }}</div>
  </div>
</template>

<script>
import Icon from '@app/Shared/Icon'
export default {
  components: { Icon },
  props: {
    title: String,
    content: String,
  },
  data() {
    return {
      show: true,
    }
  },
}
</script>
<style scoped>
.icon{
 display: inline !important;
}
</style>
