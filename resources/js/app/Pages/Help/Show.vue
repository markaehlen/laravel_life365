<template>
  <div>
    <div class="pt-2">
      <div class="container flex flex-wrap mx-auto topnav" v-if="$page.props.isProjectScreen">
        <inertia-link :href="`/help/defaults`" class="text-sm" :class="isUrl('help/defaults') ? 'active' : ''">Default Settings</inertia-link>
        <inertia-link :href="`/help/set-project`" class="text-sm" :class="isUrl('help/set-project') ? 'active' : ''">Project Details</inertia-link>
        <inertia-link :href="`/help/set-exposure`" class="text-sm" :class="isUrl('help/set-exposure') ? 'active' : ''">Exposure</inertia-link>
        <inertia-link :href="`/help/concrete-mixtures`" class="text-sm" :class="isUrl('help/concrete-mixtures') ? 'active' : ''">Concrete Mixtures</inertia-link>
        <inertia-link :href="`/help/individual-cost`" class="text-sm" :class="isUrl('help/individual-cost') ? 'active' : ''">Individual Costs</inertia-link>
        <inertia-link :href="`/help/life-cycle-cost`" class="text-sm" :class="isUrl('help/life-cycle-cost') ? 'active' : ''">Life-Cycle Cost</inertia-link>
        <inertia-link :href="`/help/service-life-report`" class="text-sm" :class="isUrl('help/service-life-report') ? 'active' : ''">Service Life Report</inertia-link>
        <inertia-link :href="`/help/lcc-report`" class="text-sm" :class="isUrl('help/lcc-report') ? 'active' : ''">LCC report</inertia-link>
        <inertia-link :href="`/help/uncertainty-analysis`" class="text-sm" :class="isUrl('help/uncertainty-analysis') ? 'active' : ''">Uncertainty Analysis</inertia-link>
        <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
          <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
        </a>
      </div>
      <div class="container px-3 mx-auto flex flex-wrap flex-col md:flex-row items-center">
        <!--Left Col-->
        <div class="flex flex-col w-full md:12 justify-center items-start text-center md:text-left">
          <h1 class="my-4 text-5xl font-bold leading-tight">
            {{ help.title }}
          </h1>
          <p class="leading-normal mb-8" v-html="help.content"></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@app/Shared/Layout'
import Icon from '@admin/Shared/Icon'

export default {
  metaInfo: { title: 'Help Window' },
  layout: Layout,
  components: {
    Icon
  },
  props: {
    help: Object,
  },
  data: {
    visibleHelp: 'defaults',
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>
<style scoped>
.topnav {
  overflow: hidden;
  background-color: #333;
}

.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #7886d7;
  color: white;
}

.topnav .icon {
  display: none;
}

@media screen and (max-width: 600px) {
  .topnav a:not(:first-child) {
    display: none;
  }
  .topnav a.icon {
    float: right;
    display: block;
  }
}

@media screen and (max-width: 600px) {
  .topnav.responsive {
    position: relative;
  }
  .topnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}
</style>
