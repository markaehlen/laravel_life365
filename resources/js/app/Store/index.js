import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    sideBarOpen: false,
    currentSystem: "SI metric",
    areaUnit: 'sq. m',
    volumeUnit: 'cub. m',
    weightUnit: 'kg',
    capacityUnit: 'L',
    defaultSubject: 's1DSlab',
    ecoParameters: null,
    tipMessage: 'Tips/help for specific fields will be displayed here.',
  },
  getters: {
    sideBarOpen: state => {
      return state.sideBarOpen
    },
    tipMessage: state => {
      return state.tipMessage
    },
    currentSystem: state => {
      return state.currentSystem
    },
    areaUnit: state => {
      return state.areaUnit
    },
    volumeUnit: state => {
      return state.volumeUnit
    },
    weightUnit: state => {
      return state.weightUnit
    },
    capacityUnit: state => {
      return state.capacityUnit
    },
    ecoParameters: state => {
      return state.ecoParameters
    },
    defaultSubject: state => {
      return state.defaultSubject
    }
  },
  mutations: {
    toggleSidebar(state) {
      state.sideBarOpen = !state.sideBarOpen
    },
    currentSystem(state, value) {
      state.currentSystem = value
    },
    areaUnit(state, value) {
      state.areaUnit = value
    },
    volumeUnit(state, value) {
      state.volumeUnit = value
    },
    weightUnit(state, value) {
      state.weightUnit = value
    },
    capacityUnit(state, value) {
      state.capacityUnit = value
    },
    ecoParameters(state, value) {
      state.ecoParameters = value
    },
    defaultSubject(state, value) {
      state.defaultSubject = value
    },
    getTipMessage(state, value) {
      state.tipMessage = value
    }
  },
  actions: {
    toggleSidebar(context) {
      context.commit('toggleSidebar')
    },
    currentSystem(context) {
      context.commit('currentSystem')
    },
    areaUnit(context) {
      context.commit('areaUnit')
    },
    volumeUnit(context) {
      context.commit('volumeUnit')
    },
    weightUnit(context) {
      context.commit('weightUnit')
    },
    capacityUnit(context) {
      context.commit('capacityUnit')
    },
    ecoParameters(context) {
      context.commit('ecoParameters')
    },
    defaultSubject(context) {
      context.commit('defaultSubject')
    },
    getTipMessage(context) {
      context.commit('tipMessage')
    }
  },
})
