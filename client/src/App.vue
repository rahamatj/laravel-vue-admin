<template>
    <div id="app">
        <div class="d-flex h-100 justify-content-center align-items-center" v-if="loading || isLoggingOut">
            <b-spinner class="spinner" variant="primary"></b-spinner>
        </div>
        <component class="component" :is="layout" v-if="!loading && !isLoggingOut">
            <transition name="fade" mode="out-in">
                <router-view></router-view>
            </transition>
        </component>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  const default_layout = "default";
  export default {
    computed: {
      ...mapGetters('login', ['isLoggingOut']),
      layout() {
        return (this.$route.meta.layout || default_layout) + '-layout';
      },
      loading() {
        return !this.$route.meta.layout
      }
    },
  }
</script>

<style lang="scss">
    @import "assets/base.scss";
</style>
