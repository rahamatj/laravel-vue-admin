<template>
    <div class="d-flex">
        <div class="header-btn-lg pr-0">
            <div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left">
                        <b-dropdown toggle-class="p-0 mr-2" menu-class="dropdown-menu-lg" variant="link" right>
                            <span slot="button-content">
                                <div class="icon-wrapper icon-wrapper-alt rounded-circle">
                                    <img width="42" class="rounded-circle" src="@/assets/images/avatars/1.jpg" alt="">
                                </div>
                            </span>
                            <button type="button" tabindex="0" class="dropdown-item">Menus</button>
                            <button type="button" tabindex="0" class="dropdown-item">Settings</button>
                            <h6 tabindex="-1" class="dropdown-header">Header</h6>
                            <button type="button" tabindex="0" class="dropdown-item">Actions</button>
                            <div tabindex="-1" class="dropdown-divider"></div>
                            <button type="button" tabindex="0" class="dropdown-item">Dividers</button>
                            <div tabindex="-1" class="dropdown-divider"></div>
                            <button id="logout" @click="logout" type="button" tabindex="0" class="dropdown-item">Logout</button>
                        </b-dropdown>
                    </div>
                    <div class="widget-content-left  ml-3 header-user-info">
                        <div class="widget-heading" v-if="user" v-text="user.name"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VuePerfectScrollbar from 'vue-perfect-scrollbar'
    import { mapGetters, mapActions, mapMutations } from 'vuex'

    import {library} from '@fortawesome/fontawesome-svg-core'
    import {
        faAngleDown,
        faCalendarAlt,
        faTrashAlt,
        faCheck,
        faFileAlt,
        faCloudDownloadAlt,
        faFileExcel,
        faFilePdf,
        faFileArchive,
        faEllipsisH,
    } from '@fortawesome/free-solid-svg-icons'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

    library.add(
        faAngleDown,
        faCalendarAlt,
        faTrashAlt,
        faCheck,
        faFileAlt,
        faCloudDownloadAlt,
        faFileExcel,
        faFilePdf,
        faFileArchive,
        faEllipsisH,
    );

    export default {
        components: {
            VuePerfectScrollbar,
            'font-awesome-icon': FontAwesomeIcon,
        },
        data: () => ({

        }),
        computed: {
          ...mapGetters('login', ['user'])
        },
        methods: {
          ...mapActions('login', ['unauthenticate']),
          ...mapMutations('login', ['SET_IS_LOGGING_OUT']),
          logout() {
            this.SET_IS_LOGGING_OUT(true)
            this.unauthenticate()
                .then(() => {
                  this.SET_IS_LOGGING_OUT(false)
                  this.$router.replace({ name: 'login' })
                })
                .catch(error => {
                  console.error(error.response.data.message)
                  this.SET_IS_LOGGING_OUT(false)
                })
          }
        }
    }


</script>
