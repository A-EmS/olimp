<template>
    <div class="d-flex">
        <div class="header-btn-lg pr-0">
            <div class="widget-content p-0">
                <div class="widget-content-wrapper">
                    <div class="widget-content-left">
                        <b-dropdown toggle-class="p-0 mr-2" menu-class="dropdown-menu-lg" variant="link" no-caret right>
                            <span slot="button-content">
                                <div class="icon-wrapper icon-wrapper-alt rounded-circle">
                                    <img width="42" class="rounded-circle" src="@/assets/images/avatars/1.jpg" alt="">
                                </div>
                            </span>
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-inner bg-info">
                                    <div class="menu-header-image opacity-2 dd-header-bg-6"></div>
                                    <div class="menu-header-content text-left">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left mr-3">
                                                    <img width="42" class="rounded-circle" src="@/assets/images/avatars/1.jpg" alt="">
                                                </div>
                                                <div class="widget-content-left">
                                                    <div class="widget-heading">{{getUserName()}}</div>
                                                    <div class="widget-subheading opacity-8"></div>
                                                </div>
                                                <div class="widget-content-right mr-2">
                                                    <button v-on:click="logout()" class="btn-pill btn-shadow btn-shine btn btn-focus">{{$store.state.t('Logout')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="scroll-area-xs" style="height: 40px;">
                                <VuePerfectScrollbar class="scrollbar-container" v-once>
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="javascript:void(0);" class="nav-link">{{$store.state.t('Settings')}}</a></li>
                                    </ul>
                                </VuePerfectScrollbar>
                            </div>
                        </b-dropdown>
                    </div>
                    <div class="widget-content-left  ml-3 header-user-info">
                        <div class="widget-heading">{{getUserName()}}</div>
                        <div class="widget-subheading"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VuePerfectScrollbar from 'vue-perfect-scrollbar'
    import VueCircle from 'vue2-circle-progress'
    import Trend from 'vuetrend';

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
    import qs from "qs";

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
            VueCircle,
            'trend': Trend,
        },
        data: () => ({
            fill1: {gradient: ["#00b09b", "#96c93d"]},
            fill2: {gradient: ["#ff0844", "#ffb199"]},
            fill3: {gradient: ["#f6d365", "#fda085"]},
            showDrawerSection: false,
        }),
        created() {

        },
        methods: {
            getUserName: function(){
                if(typeof this.$store.state.user != 'undefined' && typeof this.$store.state.user.username != 'undefined'){
                    return this.$store.state.user.username;
                } else {
                    return '';
                }
            },
            logout () {
                axios.post('/site/logout', {})
                    .then( (response) => {
                            this.$store.state.user = response.data;
                            localStorage.removeItem('currentInterfaceVocabulary');
                            this.$router.replace({ name: "login" });
                            window.location.reload();
                    })
                    .catch(function (error) {

                    });
            }
        }
    }


</script>
