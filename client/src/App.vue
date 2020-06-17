<template>
    <div id="app">
        <div class="d-flex h-100 justify-content-center align-items-center" v-if="loading">
            <b-spinner variant="primary"></b-spinner>
        </div>
        <component :is="layout" v-if="!loading">
            <transition name="fade" mode="out-in">
                <router-view></router-view>
            </transition>
        </component>
    </div>
</template>

<script>
  const default_layout = "default";
  export default {
    computed: {
      layout() {
        return (this.$route.meta.layout || default_layout) + '-layout';
      },
      loading() {
        return !this.$route.meta.layout
      }
    }
  }
</script>

<style lang="scss">
    @import "assets/base.scss";
</style>
