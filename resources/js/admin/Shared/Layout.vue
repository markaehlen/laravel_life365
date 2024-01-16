<!--Layout of entire parent panel that holds all other panels/vues-->
<template>
  <div id="layout" class="leading-normal tracking-normal">
    <portal-target name="dropdown" slim />
    <div class="flex flex-wrap">
      <Sidebar />
      <div id="main-content" class="flex flex-col w-full bg-gray-100 pl-0 lg:pl-64 min-h-screen"
        :class="sideBarOpen ? 'overlay' : ''">
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
// navigation bar on the top of the parent panel
import Navbar from '@admin/Shared/Navbar.vue'
// sidebar on the left of the parent panel that has menu choices for navigation a project
import Sidebar from '@admin/Shared/Sidebar.vue'
// footer on the bottom of the parent panel that lists version and date of tool
import Footer from '@admin/Shared/Footer.vue'
// panel for flashing messages if/when there are status or warnings or errors
import FlashMessages from '@admin/Shared/FlashMessages'

// Layout the elements of the this panel
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
