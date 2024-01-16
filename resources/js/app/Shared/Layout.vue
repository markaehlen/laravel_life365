<template>
  <div id="layout" class="leading-normal tracking-normal">
    <portal-target name="dropdown" slim />
    <div class="flex flex-wrap">
      <Sidebar />
      <div id="main-content" class="flex flex-col w-full bg-gray-100 pl-0 lg:pl-64 min-h-screen" :class="sideBarOpen ? 'overlay' : ''">
        <Navbar />
        <div class="p-3 lg:p-6 md:p-6 bg-gray-100 flex-grow">
          <!-- <div class="topnav" id="myTopnav" v-if="$page.props.isProjectScreen">
            <inertia-link :href="route('set-project')" :class="isUrl('set-project') ? 'active' : ''">Project</inertia-link>
            <inertia-link :href="route('set-exposure')" :class="isUrl('set-exposure') ? 'active' : ''">Exposure</inertia-link>
            <inertia-link href="#about">Individual Costs</inertia-link>
            <inertia-link href="#about">Concrete Mixtures</inertia-link>
            <inertia-link href="#about">Life-Cycle Cost</inertia-link>
            <inertia-link href="#about">Service Life Report</inertia-link>
            <inertia-link href="#about">LCC Report</inertia-link>
            <a href="javascript:void(0);" class="icon" @click="toggleNavbar($event)">
              <icon name="cheveron-down" class="fill-current w-4 h-4 mx-2 mt-1" />
            </a>
          </div> -->
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
import Navbar from '@app/Shared/Navbar.vue'
import Sidebar from '@app/Shared/Sidebar.vue'
import Footer from '@app/Shared/Footer.vue'
import Icon from '@app/Shared/Icon.vue'
import FlashMessages from '@app/Shared/FlashMessages'

export default {
  name: 'AppLayout',
  components: {
    Navbar,
    Sidebar,
    Footer,
    Icon,
    FlashMessages,
  },
  data() {
    return {
      date: new Date().getFullYear(),
    }
  },
  computed: {
    ...mapState(['sideBarOpen', 'ecoParameters', 'currentSystem', 'areaUnit', 'volumeUnit', 'weightUnit', 'capacityUnit']),
  },
  methods: {
    toggleSidebar() {
      this.$store.state.sideBarOpen = false
    },
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    }
  },
  mounted() {
    
    setTimeout(() => {
            const box = document.getElementById('hideden');           
            box.style.display = 'none';
            //window.location.reload();
          }, 10000); 
        } 
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
  font-size: 17px;
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
