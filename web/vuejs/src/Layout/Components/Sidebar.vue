<template>
    <div :class="sidebarbg" class="app-sidebar sidebar-shadow" @mouseover="toggleSidebarHover('add','closed-sidebar-open')" @mouseleave="toggleSidebarHover('remove','closed-sidebar-open')">
        <div class="app-header__logo">
            <div class="logo-src"/>
            <div class="header__pane ml-auto">
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" v-bind:class="{ 'is-active' : isOpen }" @click="toggleBodyClass('closed-sidebar')">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="app-sidebar-content">
            <VuePerfectScrollbar class="app-sidebar-scroll" v-once>
                <sidebar-menu showOneChild :menu="menu"/>
            </VuePerfectScrollbar>
        </div>

    </div>
</template>

<script>
    import {SidebarMenu} from 'vue-sidebar-menu'
    import VuePerfectScrollbar from 'vue-perfect-scrollbar'

    export default {
        components: {
            SidebarMenu,
            VuePerfectScrollbar
        },
        data() {
            return {
                isOpen: false,
                sidebarActive: false,

                menu: [
                    {
                        header: true,
                        title: this.$store.state.t('Main Navigation'),
                    },
                    {
                        title: this.$store.state.t('Contractors'),
                        icon: 'pe-7s-user',
                        child: [
                            {
                                href: '/entityTypes',
                                title: this.$store.state.t('Entity Types'),
                            },
                            {
                                href: '/entities',
                                title: this.$store.state.t('Entities'),
                            },
                            {
                                href: '/individuals',
                                title: this.$store.state.t('Individuals'),
                            },
                            {
                                href: '/contactTypes',
                                title: this.$store.state.t('Contact Types'),
                            },
                            {
                                href: '/contacts',
                                title: this.$store.state.t('Contacts'),
                            },
                            {
                                href: '/addressTypes',
                                title: this.$store.state.t('Address Types'),
                            },
                            {
                                href: '/addresses',
                                title: this.$store.state.t('Addresses'),
                            },
                        ]
                    },
                    {
                        title: this.$store.state.t('Locations'),
                        icon: 'pe-7s-global',
                        child: [
                            {
                                href: '/worldParts',
                                title: this.$store.state.t('World Parts'),
                            },
                            {
                                href: '/countries',
                                title: this.$store.state.t('Countries'),
                            },
                            {
                                href: '/regions',
                                title: this.$store.state.t('Regions'),
                            },
                            {
                                href: '/cities',
                                title: this.$store.state.t('Cities'),
                            },
                        ]
                    },
                    {
                        title: this.$store.state.t('Core Settings'),
                        icon: 'pe-7s-browser',
                        child: [
                            {
                                href: '/languages',
                                title: this.$store.state.t('Languages'),
                            },
                            {
                                href: '/vocabularies',
                                title: this.$store.state.t('Vocabularies'),
                            },
                            {
                                href: '/users',
                                title: this.$store.state.t('Users'),
                            },
                        ]
                    },
                    {
                        header: true,
                        title: this.$store.state.t('Modules'),
                    },
                    {
                        title: this.$store.state.t('Engineering'),
                        icon: 'pe-7s-plugin',
                        child: [
                            {
                                href: '/projectStages',
                                title: this.$store.state.t('Project Stages'),
                            },
                            {
                                href: '/projectParts',
                                title: this.$store.state.t('Project Parts'),
                            },
                        ]
                    },
                ],
                collapsed: true,

                windowWidth: 0,

            }
        },
        props: {
            sidebarbg: String,

        },
        created: function () {

        },
        methods: {

            toggleBodyClass(className) {
                const el = document.body;
                this.isOpen = !this.isOpen;

                if (this.isOpen) {
                    el.classList.add(className);
                } else {
                    el.classList.remove(className);
                }
            },
            toggleSidebarHover(add, className) {
                const el = document.body;
                this.sidebarActive = !this.sidebarActive;

                this.windowWidth = document.documentElement.clientWidth;

                if (this.windowWidth > '992') {
                    if (add === 'add') {
                        el.classList.add(className);
                    } else {
                        el.classList.remove(className);
                    }
                }
            },
            getWindowWidth() {
                const el = document.body;

                this.windowWidth = document.documentElement.clientWidth;

                if (this.windowWidth < '1350') {
                    el.classList.add('closed-sidebar', 'closed-sidebar-md');
                } else {
                    el.classList.remove('closed-sidebar', 'closed-sidebar-md');
                }
            },
        },
        mounted() {
            this.$nextTick(function () {
                window.addEventListener('resize', this.getWindowWidth);

                //Init
                this.getWindowWidth()
            })
        },

        beforeDestroy() {
            window.removeEventListener('resize', this.getWindowWidth);
        }
    }
</script>
