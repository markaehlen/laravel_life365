<template>
  <div id="home">
    <img class="hero-banner" src="/assets/app/images/hero.png" />
  </div>
</template>
<script>
import Layout from '@app/Shared/Layout'
import Chart from '@app/Shared/Chart'
import Icon from '@app/Shared/Icon'

export default {
  name: 'Index',
  metaInfo: { title: 'Home' },
  components: {
    Icon,
    Chart,
  },
  layout: Layout,
  props: {
    totalUsersCount: Number,
    activeUsersCount: Number,
    newEnquiriesCount: Number,
    userStatistics: Object,
    enquiryStatistics: Object,
  },
}
</script>
<style scoped>
.hero-banner {
  max-width: 100%;
  margin: auto;
}
</style>
