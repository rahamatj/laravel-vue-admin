import Vue from 'vue'
import Router from 'vue-router'
import guards from '@/guards'

Vue.use(Router);

export default new Router({
  scrollBehavior() {
    return window.scrollTo({top: 0, behavior: 'smooth'});
  },
  mode: 'history',
  routes: [

    // Login

    {
      path: '/',
      name: 'login',
      meta: {
        title: 'Login',
        layout: 'userpages'
      },
      component: () => import('../Auth/Login.vue'),
      beforeEnter: guards.redirectToDashboard
    },

    {
      path: '/password/reset',
      name: 'password.request',
      meta: {
        title: 'Reset Password',
        layout: 'userpages'
      },
      component: () => import('../Auth/Passwords/Email.vue'),
      beforeEnter: guards.redirectToDashboard
    },

    {
      path: '/password/reset/:token',
      name: 'password.reset',
      meta: {
        title: 'Reset Password',
        layout: 'userpages'
      },
      component: () => import('../Auth/Passwords/Reset.vue'),
      props: true,
      beforeEnter: guards.redirectToDashboard
    },

    // Dashboard

    {
      path: '/dashboard',
      name: 'dashboard',
      meta: {
        title: 'Dashboard',
        layout: 'default'
      },
      component: () => import('../Dashboard/Dashboard.vue'),
      beforeEnter: guards.redirectToLogin
    },

    // Pages

    {
      path: '/pages/login-boxed',
      name: 'login-boxed',
      meta: {layout: 'userpages'},
      component: () => import('../DemoPages/UserPages/LoginBoxed.vue'),
    },
    {
      path: '/pages/register-boxed',
      name: 'register-boxed',
      meta: {layout: 'userpages'},
      component: () => import('../DemoPages/UserPages/RegisterBoxed.vue'),
    },
    {
      path: '/pages/forgot-password-boxed',
      name: 'forgot-password-boxed',
      meta: {layout: 'userpages'},
      component: () => import('../DemoPages/UserPages/ForgotPasswordBoxed.vue'),
    },

    // Elements

    {
      path: '/elements/buttons-standard',
      name: 'buttons-standard',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Buttons/Standard.vue'),
    },
    {
      path: '/elements/dropdowns',
      name: 'dropdowns',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Dropdowns.vue'),
    },
    {
      path: '/elements/icons',
      name: 'icons',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Icons.vue'),
    },
    {
      path: '/elements/badges-labels',
      name: 'badges',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Badges.vue'),
    },
    {
      path: '/elements/cards',
      name: 'cards',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Cards.vue'),
    },
    {
      path: '/elements/list-group',
      name: 'list-group',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/ListGroups.vue'),
    },
    {
      path: '/elements/timelines',
      name: 'timeline',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Timeline.vue'),
    },
    {
      path: '/elements/utilities',
      name: 'utilities',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Elements/Utilities.vue'),
    },

    // Components

    {
      path: '/components/tabs',
      name: 'tabs',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Tabs.vue'),
    },
    {
      path: '/components/accordions',
      name: 'accordions',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Accordions.vue'),
    },
    {
      path: '/components/modals',
      name: 'modals',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Modals.vue'),
    },
    {
      path: '/components/progress-bar',
      name: 'progress-bar',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/ProgressBar.vue'),
    },
    {
      path: '/components/tooltips-popovers',
      name: 'tooltips-popovers',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/TooltipsPopovers.vue'),
    },
    {
      path: '/components/carousel',
      name: 'carousel',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Carousel.vue'),
    },
    {
      path: '/components/pagination',
      name: 'pagination',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Pagination.vue'),
    },
    {
      path: '/components/maps',
      name: 'maps',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Components/Maps.vue'),
    },

    // Tables

    {
      path: '/tables/regular-tables',
      name: 'regular-tables',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Tables/RegularTables.vue'),
    },

    // Dashboard Widgets

    {
      path: '/widgets/chart-boxes-3',
      name: 'chart-boxes-3',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Widgets/ChartBoxes3.vue'),
    },

    // Forms

    {
      path: '/forms/controls',
      name: 'controls',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Forms/Elements/Controls.vue'),
    },
    {
      path: '/forms/layouts',
      name: 'layouts',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Forms/Elements/Layouts.vue'),
    },
    // Charts

    {
      path: '/charts/chartjs',
      name: 'chartjs',
      meta: {layout: 'default'},
      component: () => import('../DemoPages/Charts/Chartjs.vue'),
    },
  ]
})
