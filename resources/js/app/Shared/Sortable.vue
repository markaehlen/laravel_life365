<template>
  <th class="cursor-pointer" v-bind="$attrs" @click="sortColumn">
    {{ label }}
    <icon :name="sort == `${column},desc` ? 'sort-desc' : 'sort-asc'" class="fill-current h-4 w-4 inline-block" :class="{ 'text-gray-800': sort == `${column},asc` || sort == `${column},desc`, 'text-gray-400': sort != `${column},asc` && sort != `${column},desc` }" />
  </th>
</template>

<script>
import Icon from '@admin/Shared/Icon'
export default {
  components: {
    Icon,
  },
  props: {
    label: String,
    column: String,
    sort: {
      type: String,
      default() {
        return `${this.column},none`
      },
    },
  },
  methods: {
    sortColumn() {
      const sortVal = this.sort == (`${this.column},asc` || `${this.column},none`) ? `${this.column},desc` : `${this.column},asc`
      this.$emit('input', sortVal)
    },
  },
}
</script>
