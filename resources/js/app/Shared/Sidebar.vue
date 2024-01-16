<template>
  <!-- give the sidebar z-50 class so its higher than the navbar if you want to see the logo -->
  <!-- you will need to add a little "X" button next to the logo in order to close it though -->
  <div id="main-nav" class="w-1/2 md:w-1/3 lg:w-64 fixed md:top-0 md:left-0 h-screen lg:block bg-current bg-gradient-to-b from-indigo-400 to-indigo-700 z-30 shadow" :class="sideBarOpen ? '' : 'hidden'">
    <div class="w-full h-20 bg-indigo-700 flex px-4 items-center mb-2" style="background-color: white">
      <inertia-link :href="route('home')">
        <p class="font-semibold text-3xl text-white pl-4">
          <logo class="life-logo" />
        </p>
      </inertia-link>
    </div>
    <div class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">PROJECT</p>
      <!-- && !$page.props.currentAnalysis -->
      <div v-if="$page.props.isProjectScreen " class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('defaults') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('defaults')">
          <icon name="settings" class="h-6 w-6 fill-current mr-2" />
          <span>Default Settings</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('set-project') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('set-project')">
          <icon name="document" class="h-6 w-6 fill-current mr-2" />
          <span>Project Details</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('set-exposure') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('set-exposure')">
          <icon name="exposures" class="h-6 w-6 fill-current mr-2" />
          <span>Exposure</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('concrete-mixtures') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('concrete-mixtures')">
          <icon name="units" class="h-6 w-6 fill-current mr-2" />
          <span>Concrete Mixtures</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('service-life-report') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('service-life-report')">
          <icon name="reports" class="h-6 w-6 fill-current mr-2" />
          <span>Service Life Report</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('individual-cost') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('individual-cost')">
          <icon name="dollar" class="h-6 w-6 fill-current mr-2" />
          <span>Individual Costs</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('life-cycle-cost') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('life-cycle-cost')">
          <icon name="sort-asc" class="h-6 w-6 fill-current mr-2" />
          <span>Life-Cycle Cost</span>
        </span>
      </div>
      
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('lcc-report') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('lcc-report')">
          <icon name="unit-systems" class="h-6 w-6 fill-current mr-2" />
          <span>LCC Report</span>
        </span>
      </div>
      <div v-if="!$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/dashboard') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar()" :href="route('set-project')">
          <icon name="plus" class="h-6 w-6 fill-current mr-2" />
          <span>{{ $page.props.currentAnalysis ? 'Back to Project' : 'New Project' }}</span>
        </inertia-link>
      </div>
      <div v-if="!$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/dashboard') ? 'bg-gray-200 text-gray-700' : ''">
        <input type="file" id="upload" name="upload" @change="importProject()" style="visibility: hidden; width: 1px; height: 1px" />
        <a class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar()" :href="`#`" onclick="document.getElementById('upload').click(); return false">
          <icon name="upload" class="h-6 w-6 fill-current mr-2" />
          <span>Import project</span>
        </a>
      </div>
      
      <div v-if="!$page.props.isProjectScreen && $page.props.auth.user" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('overview') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar()" :href="`/my-projects`">
          <icon name="folder-open" class="h-6 w-6 fill-current mr-2" />
          <span>Open Existing Project</span>
        </inertia-link>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('save-project') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('save-project')">
          <icon name="save" class="h-6 w-6 fill-current mr-2" />
          <span>Save Project</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('new-project') ? 'bg-gray-200 text-gray-700' : ''">
        <span class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar('new-project')">
          <icon name="save" class="h-6 w-6 fill-current mr-2" />
          <span>Save as New</span>
        </span>
      </div>
      <div v-if="$page.props.isProjectScreen && $page.props.currentAnalysis" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/dashboard') ? 'bg-gray-200 text-gray-700' : ''">
        <a class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar()" :href="route('export.project')">
          <icon name="sort-desc" class="h-6 w-6 fill-current mr-2" />
          <span>Export Project</span>
        </a>
      </div>
      <div v-if="$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('admin/dashboard') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" :href="route('close.project')">
          <icon name="close" class="h-6 w-6 fill-current mr-2" />
          <span>Close Project</span>
        </inertia-link>
      </div>
    </div>
    <div class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">MORE</p>
      <div v-if="$page.props.isProjectScreen && !$page.url.substr(1).startsWith('help')" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('help') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar(['help-window', $page.url.substr(1)])" :href="`/help/${$page.url.substr(1)}`">
          <icon name="question" class="h-6 w-6 fill-current mr-2" />
          <span>Help for this window</span>
        </inertia-link>
      </div>
      <div v-if="!$page.props.isProjectScreen" class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" :class="isUrl('overview') ? 'bg-gray-200 text-gray-700' : ''">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar()" :href="`/overview`">
          <icon name="question" class="h-6 w-6 fill-current mr-2" />
          <span>Overview</span>
        </inertia-link>
      </div>
      <div class="w-full flex items-center text-white hover:bg-gray-200 hover:text-indigo-600 cursor-pointer" v-for="page in $page.props.cms_pages" :key="page.id">
        <inertia-link class="flex group w-full pl-4 py-1 text-sm sidebar_link" @click="hideSideBar(['pages', page.slug])" :href="route('pages', page.slug)">
          <icon name="document" class="h-6 w-6 fill-current mr-2" />
          <span>{{ page.title }}</span>
        </inertia-link>
      </div>
    </div>
    <!-- <div class="mb-2 px-4">
      <p class="py-2 text-sm font-semibold text-white">TIPS</p>
      <p class="py-2 text-sm text-white tips-container">{{ tipMessage }}</p>
    </div> -->
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Icon from '@admin/Shared/Icon'
  import Logo from '@admin/Shared/Logo'
  import { Inertia } from '@inertiajs/inertia'
  import swal from 'sweetalert'
  import { EventBus } from '@app/event-bus'
  export default {
    name: 'Sidebar',
    components: {
      Icon,
      Logo,
    },
    computed: {
      ...mapState(['sideBarOpen', 'tipMessage']),
    },
    methods: {
      hideSideBar(intendedUrl = null) {
        
        try{
        console.log('save',intendedUrl)
        EventBus.$emit('navigateApplicationTo', intendedUrl);
        this.$store.state.sideBarOpen = false
        }catch(e){
          console.log({e})
        }
        
      },
      isUrl(...urls) {
        let currentUrl = this.$page.url.substr(1)
        if (urls[0] === '') {
          return currentUrl === ''
        }
        return urls.filter((url) => currentUrl.startsWith(url)).length
      },
      importProject() {
        var formData = new FormData()
        var jsonFile = document.querySelector('#upload')
        formData.append('file', jsonFile.files[0])
        // axios
        //   .post('import-project', formData, {
        //     headers: {
        //       'Content-Type': 'multipart/form-data',
        //     },
        //   })
        //   .then(function (response) {
        //     console.log(response.data)
        //   })
        //   .catch(function (error) {
        //     if (error.response) {
        //       swal('Oh no!', error.response.data.message, 'error')
        //     }
        //   })
        Inertia.post(`import-project`, {
          file: jsonFile.files[0],
        },{
          forceFormData: true,
        })
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
