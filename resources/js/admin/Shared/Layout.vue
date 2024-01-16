<template>
  <div id="layout" class="leading-normal tracking-normal">
    <portal-target name="dropdown" slim />
    <div class="flex flex-wrap">
      <Sidebar />
      <div id="main-content" class="flex flex-col w-full bg-gray-100 pl-0 lg:pl-64 min-h-screen" :class="sideBarOpen ? 'overlay' : ''">
        <Navbar />
        <div class="p-6 bg-gray-100 flex-grow">
          <flash-messages />
          <slot />
        </div>
        <Footer />
      </div>
      <div id="overlay" v-if="$store.state.sideBarOpen" @click="toggleSidebar()"></div>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex'
import Navbar from '@admin/Shared/Navbar.vue'
import Sidebar from '@admin/Shared/Sidebar.vue'
import Footer from '@admin/Shared/Footer.vue'
import FlashMessages from '@admin/Shared/FlashMessages'

export default {
  name: 'DashboardPage',
  components: {
    Navbar,
    Sidebar,
    Footer,
    FlashMessages,
  },
  data() {
    return {
      date: new Date().getFullYear(),
    }
  },
  computed: {
    ...mapState(['sideBarOpen']),
  },
  methods: {
    toggleSidebar() {
      this.$store.dispatch('toggleSidebar')
    },
  },
}
</script>

<style scoped>
#overlay {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 2;
  cursor: pointer;
}
</style>
