<template>
  <div id="preview">
    <trashed-message v-if="sublocation.deleted_at" class="mb-5" :can-restore="authorize.restore" @restore="restore"> This sub-location has been deleted. </trashed-message>
    <div class="w-full border-b block 2xl:flex xl:flex lg:flex md:flex flex-nowrap justify-between items-center bg-gray-50 p-5 mb-5">
      <h1 class="font-semibold text-2xl">Sub-Locations</h1>
      <div class="flex">
        <!-- breadcrumb -->
        <nav class="text-sm font-semibold" aria-label="Breadcrumb">
          <ol class="list-none p-0 inline-flex">
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.dashboard')" class="text-gray-700"> Home </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center text-indigo-500">
              <inertia-link :href="route('admin.sublocations.index')" class="text-gray-700"> Sub-Locations </inertia-link>
              <icon name="cheveron-right" class="fill-current w-4 h-4 mx-2 mt-1" />
            </li>
            <li class="flex items-center">
              <a href="javascript:void" class="text-gray-600">Preview</a>
            </li>
          </ol>
        </nav>
        <!-- breadcrumb end -->
      </div>
    </div>
    <div class="bg-white overflow-x-auto">
      <table class="w-full text-left">
        <tbody class="text-gray-600 text-sm font-light">
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Name</td>
            <td class="py-3 px-6">
              <span>{{ sublocation.name }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Parent Location</td>
            <td class="py-3 px-6">
              <span>{{ sublocation.location.name }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Exposures</td>
            <td class="py-3 px-6">
              <li v-for="(exposure, index) in sublocation.exposures" :key="index">{{ exposure.name }}</li>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Average Temperature (°C)</td>
            <td class="py-3 px-6">
              <span>{{ sublocation.avg_temp }}°C</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Is Marine?</td>
            <td class="py-3 px-6">
              <span>{{ sublocation.is_marine ? 'Yes' : 'No' }}</span>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Monthly Temperature Details</td>
            <td class="py-3 px-6">
              <li>January : {{ sublocation.jan_temp }}°C</li>
              <li>February : {{ sublocation.feb_temp }}°C</li>
              <li>March : {{ sublocation.mar_temp }}°C</li>
              <li>April : {{ sublocation.apr_temp }}°C</li>
              <li>May : {{ sublocation.may_temp }}°C</li>
              <li>June : {{ sublocation.jun_temp }}°C</li>
              <li>July : {{ sublocation.jul_temp }}°C</li>
              <li>August : {{ sublocation.aug_temp }}°C</li>
              <li>September : {{ sublocation.sep_temp }}°C</li>
              <li>October : {{ sublocation.oct_temp }}°C</li>
              <li>November : {{ sublocation.nov_temp }}°C</li>
              <li>December : {{ sublocation.dec_temp }}°C</li>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Other Related Details</td>
            <td class="py-3 px-6">
              <li>Snow Days : {{ sublocation.snow_day }}</li>
              <li>Mean RH (%) : {{ sublocation.mean_rh_perc }}%</li>
              <li>Build Up : {{ sublocation.build_up }}%</li>
              <li>Standard Deviation : {{ sublocation.std_dev }}%</li>
              <li>Max CS : {{ sublocation.max_cs }}%</li>
              <li>Cost Of 0.42 : ${{ sublocation.cost_zerofourtwo }}</li>
              <li>Cost Of Black Steel : ${{ sublocation.cost_black }}</li>
              <li>Cost Of Stainless Steel : ${{ sublocation.cost_stainless }}</li>
              <li>Cost Of Epoxy Steel : ${{ sublocation.cost_epoxy }}</li>
              <li>Cost Of Membrane : ${{ sublocation.membrane }}</li>
              <li>Cost Of Sealer : ${{ sublocation.cost_sealer }}</li>
            </td>
          </tr>
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="font-semibold text-sm leading-normal py-3 px-6">Created At</td>
            <td class="py-3 px-6">
              <span>{{ sublocation.created_at }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="px-5 py-5 bg-gray-50 border-t border-gray-100 flex items-center">
      <action-back :href="route('admin.sublocations.index')" />
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert'
import Icon from '@admin/Shared/Icon'
import Layout from '@admin/Shared/Layout'
import ActionBack from '@admin/Shared/ActionBack'
import TrashedMessage from '@admin/Shared/TrashedMessage'

export default {
  name: 'SublocationPreview',
  metaInfo() {
    return {
      name: this.sublocation.name,
    }
  },
  components: {
    Icon,
    ActionBack,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    sublocation: Object,
    authorize: Object,
  },
  methods: {
    restore() {
      const vInstance = this
      if (!vInstance.authorize.restore) return
      swal('Are you sure you want to restore this sub-location?', {
        buttons: ['No', 'Yes'],
      }).then((value) => {
        if (value) {
          vInstance.$inertia.put(vInstance.route('admin.sublocations.restore', vInstance.sublocation.id))
        }
      })
    },
  },
}
</script>
