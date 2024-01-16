import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { App, plugin } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress/src'
import { Inertia } from '@inertiajs/inertia'
import VueKonva from 'vue-konva';
import { ValidationProvider } from 'vee-validate';
import { ValidationObserver } from 'vee-validate';
import UUID from "vue-uuid";
import VModal from 'vue-js-modal'
import VTooltip from 'v-tooltip'
import FAIcon from 'vue-awesome/components/Icon'
import NProgress from 'nprogress'
import 'vue-awesome/icons'

import store from './Store'

Vue.config.productionTip = false
Vue.prototype.$route = route
Vue.mixin(
  {
    methods: { 
      route: window.route,
      toggleNavbar: function(event) {
          if (event && event.target.className == 'icon') {
            event.target.parentElement.classList.toggle('responsive')
          }else if(event){
            event.target.parentElement.parentElement.classList.toggle('responsive')
          }
      }
     } 
  }
)
Vue.use(plugin)
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(VueKonva)
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Vue.use(UUID);
Vue.use(VModal)
Vue.use(VTooltip)
Vue.component('v-icon', FAIcon)



InertiaProgress.init({
  // The delay after which the progress bar will
  // appear during navigation, in milliseconds.
  delay: 250,

  // The color of the progress bar.
  color: '#29d',

  // Whether to include the default NProgress styles.
  includeCSS: true,

  // Whether the NProgress spinner will be shown.
  showSpinner: true,
})

// Add a request interceptor
axios.interceptors.request.use(function (config) {
  // Do something before request is sent
  NProgress.start();
  return config;
}, function (error) {
  // Do something with request error
  NProgress.done();
  console.error(error)
  return Promise.reject(error);
});

// Add a response interceptor
axios.interceptors.response.use(function (response) {
  // Do something with response data
  NProgress.done();
  return response;
}, function (error) {
  // Do something with response error
  NProgress.done();
  console.error(error)
  return Promise.reject(error);
});

const el = document.getElementById('app')
new Vue({
  metaInfo() {
    let page = JSON.parse(el.dataset.page)
    return {
      titleTemplate: title => (title ? `${title} - ${page.props.settings.app_name}` : page.props.settings.app_name),
    }
  },
  render: h =>
    h(App, {
      props: {
        initialPage: JSON.parse(el.dataset.page),
        resolveComponent: name => import(`@app/Pages/${name}`).then(module => module.default),
      },
    }),
  store,
}).$mount(el)
