<template>
  <div>
    <div class="pt-2">
      <div class="container px-3 mx-auto flex flex-wrap flex-col md:flex-row items-center">
        <!--Left Col-->
        <div class="flex flex-col w-full md:12 justify-center items-start text-center md:text-left">
          <h1 class="my-4 text-5xl font-bold leading-tight text-center">
            {{cms_page.title}}
          </h1>
          <p class="leading-normal mb-8" style="text-align: left;" v-html="cms_page.content">          
          </p>          
        </div>        
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@app/Shared/Layout'

export default {
  metaInfo: { title: 'Sample' },
  layout: Layout,
  props:{
    cms_page:Object
  }
}
</script>
