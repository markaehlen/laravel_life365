<template>
  <!-- give the sidebar z-50 class so its higher than the navbar if you want to see the logo -->
  <!-- you will need to add a little "X" button next to the logo in order to close it though -->
  <div id="main-nav" class="w-1/2 md:w-1/3 lg:w-64 fixed md:top-0 md:left-0 h-screen lg:block bg-current bg-gradient-to-b from-indigo-400 to-indigo-700 z-30 shadow" :class="sideBarOpen ? '' : 'hidden'">
    <div class="w-full h-20 bg-indigo-700 flex px-4 items-center mb-2" style="background-color: white">
      <inertia-link :href="route('admin.dashboard')">
        <p class="font-semibold text-3xl text-white pl-4">
          <logo class="life-logo" />
        </p>
      </inertia-link>
    </div>
    <div class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">MAIN</p>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/dashboard') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.dashboard')">
          <icon name="home" class="h-6 w-6 fill-current mr-2" />
          <span>Dashboard</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.can.access_users" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/users') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.users.index')">
          <icon name="users" class="h-6 w-6 fill-current mr-2" />
          <span>Users</span>
        </inertia-link>
      </div>
      <!-- <div v-if="$page.props.can.access_roles" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/roles') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2" :href="route('admin.roles.index')">
          <icon name="role" class="h-6 w-6 fill-current mr-2" />
          <span>Roles</span>
        </inertia-link>
      </div> -->
      <!-- <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/reports') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2" :href="route('admin.reports')">
          <icon name="reports" class="h-6 w-6 fill-current mr-2" />
          <span>Reports</span>
        </inertia-link>
      </div> -->
    </div>
    <div class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">CORE PARAMETERS</p>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.systems.index')" :class="isUrl('admin/systems') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="unit-systems" class="h-6 w-6 fill-current mr-2" />
          <span>Unit Systems</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.units.index')" :class="isUrl('admin/units') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="units" class="h-6 w-6 fill-current mr-2" />
          <span>Units</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.locations.index')" :class="isUrl('admin/locations') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="locations" class="h-6 w-6 fill-current mr-2" />
          <span>Locations</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.exposures.index')" :class="isUrl('admin/exposures') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="exposures" class="h-6 w-6 fill-current mr-2" />
          <span>Exposures</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.sublocations.index')" :class="isUrl('admin/sublocations') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="sub-locations" class="h-6 w-6 fill-current mr-2" />
          <span>Sub-Locations</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.ecoparameters.index')" :class="isUrl('admin/ecoparameters') ? 'bg-gray-200 text-gray-700' : ''">
          <icon name="dollar" class="h-6 w-6 fill-current mr-2" />
          <span>Economic Parameters</span>
        </inertia-link>
      </div>
    </div>
    <div v-if="$page.props.can.access_faqs || $page.props.can.access_pages || $page.props.can.access_settings || $page.props.can.access_enquiries || $page.props.can.access_email_templates || $page.props.can.access_testimonials" class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">MISC</p>
      <!-- <div v-if="$page.props.can.access_faqs" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/faqs') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2" :href="route('admin.faqs.index')">
          <icon name="question" class="h-6 w-6 fill-current mr-2" />
          <span>Faq's</span>
        </inertia-link>
      </div> -->
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/helpwindows') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.helpwindows.index')">
          <icon name="unit-systems" class="h-6 w-6 fill-current mr-2" />
          <span>Help Windows</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/help-tips') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.help-tips.index')">
          <icon name="question" class="h-6 w-6 fill-current mr-2" />
          <span>Help Tips</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.can.access_pages" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/pages') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.pages.index')">
          <icon name="document" class="h-6 w-6 fill-current mr-2" />
          <span>CMS Pages</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.can.access_settings" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/settings') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.settings')">
          <icon name="settings" class="h-6 w-6 fill-current mr-2" />
          <span>Settings</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.can.access_enquiries" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/enquiries') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.enquiries.index')">
          <icon name="enquiries" class="h-6 w-6 fill-current mr-2" />
          <span>Website Requests</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.can.access_email_templates" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/email-templates') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2 sidebar_link" @click="hideSideBar()" :href="route('admin.email-templates.index')">
          <icon name="testimonial" class="h-6 w-6 fill-current mr-2" />
          <span>Email Templates</span>
        </inertia-link>
      </div>
      <!-- <div v-if="$page.props.can.access_testimonials" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/testimonials') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 h-10 pt-2" :href="route('admin.testimonials.index')">
          <icon name="testimonial" class="h-6 w-6 fill-current mr-2" />
          <span>Testimonials</span>
        </inertia-link> -->
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Icon from '@admin/Shared/Icon'
import Logo from '@admin/Shared/Logo'
export default {
  name: 'Sidebar',
  components: {
    Icon,
    Logo,
  },
  computed: {
    ...mapState(['sideBarOpen']),
  },
  methods: {
    hideSideBar() {
      this.$store.state.sideBarOpen = false
    },
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
.life-logo {
  max-height: 70px;
  margin-left: 25px;
}
</style>
