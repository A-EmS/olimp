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
    import qs from "qs";
    import axios from 'axios';

    export default {
        components: {
            SidebarMenu,
            VuePerfectScrollbar
        },
        data() {
            return {
                isOpen: false,
                sidebarActive: false,

                // filteredAccessMenu: [],

                menu: [
                    {
                        nonDeletable: true,
                        header: true,
                        title: this.$store.state.t('Main Navigation'),
                    },
                    {
                        title: this.$store.state.t('Contractors'),
                        icon: 'pe-7s-user',
                        attributes: {'hidden': true},
                        child: [
                            {
                                href: '/individuals',
                                title: this.$store.state.t('Individuals'),
                                accessLabelId: 'individuals',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/entities',
                                title: this.$store.state.t('Entities'),
                                accessLabelId: 'entities',
                                attributes: {'hidden': true}
                            },
                        ]
                    },
                    {
                        title: this.$store.state.t('Core Settings'),
                        icon: 'pe-7s-browser',
                        attributes: {'hidden': true},
                        child: [
                            {
                                title: this.$store.state.t('Contractors'),
                                attributes: {'hidden': true},
                                child: [
                                    {
                                        href: '/entityTypes',
                                        title: this.$store.state.t('Entity Types'),
                                        accessLabelId: 'entityTypes',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/personal',
                                        title: this.$store.state.t('Personal'),
                                        accessLabelId: 'personal',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/contactTypes',
                                        title: this.$store.state.t('Contact Types'),
                                        accessLabelId: 'contactTypes',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/contacts',
                                        title: this.$store.state.t('Contacts'),
                                        accessLabelId: 'contacts',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/addressTypes',
                                        title: this.$store.state.t('Address Types'),
                                        accessLabelId: 'addressTypes',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/addresses',
                                        title: this.$store.state.t('Addresses'),
                                        accessLabelId: 'addresses',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/ownCompanies',
                                        title: this.$store.state.t('Own Companies'),
                                        accessLabelId: 'ownCompanies',
                                        attributes: {'hidden': true}
                                    },
                                ]
                            },
                            {
                                title: this.$store.state.t('Locations'),
                                attributes: {'hidden': true},
                                child: [
                                    {
                                        href: '/worldParts',
                                        title: this.$store.state.t('World Parts'),
                                        accessLabelId: 'worldParts',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/countries',
                                        title: this.$store.state.t('Countries'),
                                        accessLabelId: 'countries',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/regions',
                                        title: this.$store.state.t('Regions'),
                                        accessLabelId: 'regions',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/cities',
                                        title: this.$store.state.t('Cities'),
                                        accessLabelId: 'cities',
                                        attributes: {'hidden': true}
                                    },
                                ]
                            },
                            {
                                title: this.$store.state.t('Finances'),
                                attributes: {'hidden': true},
                                child: [
                                    {
                                        href: '/banks',
                                        title: this.$store.state.t('Banks'),
                                        accessLabelId: 'banks',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/currencies',
                                        title: this.$store.state.t('Currencies'),
                                        accessLabelId: 'currencies',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/financeClasses',
                                        title: this.$store.state.t('Finance Classes'),
                                        accessLabelId: 'financeClasses',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/tills',
                                        title: this.$store.state.t('Tills'),
                                        accessLabelId: 'tills',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/documentsStatuses',
                                        title: this.$store.state.t('Documents Statuses'),
                                        accessLabelId: 'documentsStatuses',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/documentTypes',
                                        title: this.$store.state.t('Documents Types'),
                                        accessLabelId: 'documentTypes',
                                        attributes: {'hidden': true}
                                    },
                                ]
                            },
                            {
                                href: '/languages',
                                title: this.$store.state.t('Languages'),
                                accessLabelId: 'languages',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/vocabularies',
                                title: this.$store.state.t('Vocabularies'),
                                accessLabelId: 'vocabularies',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/calendar',
                                title: this.$store.state.t('Calendar'),
                                accessLabelId: 'calendar',
                                attributes: {'hidden': true}
                            },
                            {
                              href: '/tags',
                              title: this.$store.state.t('Tags'),
                              accessLabelId: 'tags',
                              attributes: {'hidden': true}
                            },
                            {
                              href: '/patterns',
                              title: this.$store.state.t('Patterns'),
                              accessLabelId: 'patterns',
                              attributes: {'hidden': true}
                            },
                            {
                              href: '/priceLists',
                              title: this.$store.state.t('Price Lists'),
                              accessLabelId: 'priceLists',
                              attributes: {'hidden': true}
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
                        attributes: {'hidden': true},
                        child: [
                            {
                                href: '/projects',
                                title: this.$store.state.t('Projects'),
                                accessLabelId: 'projects',
                                attributes: {'hidden': true}
                            },
                            {
                                title: this.$store.state.t('Reports'),
                                attributes: {'hidden': true},
                                child: [
                                    {
                                        href: '/projectCompleting',
                                        title: this.$store.state.t('Project Completing'),
                                        accessLabelId: 'projectCompleting',
                                        attributes: {'hidden': true}
                                    },
                                ]
                            },
                            {
                              href: '/constructionTypes',
                              title: this.$store.state.t('Construction Types'),
                              accessLabelId: 'constructionTypes',
                              attributes: {'hidden': true}
                            },
                            {
                              href: '/objectMainValues',
                              title: this.$store.state.t('Object Main Values'),
                              accessLabelId: 'objectMainValues',
                              attributes: {'hidden': true}
                            },
                            {
                              href: '/prices',
                              title: this.$store.state.t('Prices'),
                              accessLabelId: 'prices',
                              attributes: {'hidden': true}
                            },
                            {
                              href: '/requests',
                              title: this.$store.state.t('Requests'),
                              accessLabelId: 'requests',
                              attributes: {'hidden': true}
                            },
                            {
                                title: this.$store.state.t('Settings'),
                                attributes: {'hidden': true},
                                child: [
                                    {
                                        href: '/projectStages',
                                        title: this.$store.state.t('Project Stages'),
                                        accessLabelId: 'projectStages',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/projectParts',
                                        title: this.$store.state.t('Project Parts'),
                                        accessLabelId: 'projectParts',
                                        attributes: {'hidden': true}
                                    },
                                    {
                                        href: '/projectStatuses',
                                        title: this.$store.state.t('Project Statuses'),
                                        accessLabelId: 'projectStatuses',
                                        attributes: {'hidden': true}
                                    },
                                ]
                            },
                        ]
                    },
                    {
                        title: this.$store.state.t('Finances'),
                        icon: 'pe-7s-plugin',
                        attributes: {'hidden': true},
                        child: [
                            {
                                href: '/till',
                                title: this.$store.state.t('Till'),
                                accessLabelId: 'till',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/invoices',
                                title: this.$store.state.t('Invoices'),
                                accessLabelId: 'invoices',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/financeBook',
                                title: this.$store.state.t('Finance Book'),
                                accessLabelId: 'financeBook',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/products',
                                title: this.$store.state.t('Products'),
                                accessLabelId: 'products',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/services',
                                title: this.$store.state.t('Services'),
                                accessLabelId: 'services',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/financeDocuments',
                                title: this.$store.state.t('Finance Documents'),
                                accessLabelId: 'financeDocuments',
                                attributes: {'hidden': true}
                            },
                            {
                                href: '/currencyExchangeRates',
                                title: this.$store.state.t('Currency Exchange Rates'),
                                accessLabelId: 'currencyExchangeRates',
                                attributes: {'hidden': true}
                            },
                          {
                            title: this.$store.state.t('Reports'),
                            attributes: {'hidden': true},
                            child: [
                              {
                                href: '/consolidatedReport',
                                title: this.$store.state.t('Consolidated Report'),
                                accessLabelId: 'consolidatedReport',
                                attributes: {'hidden': true}
                              },
                            ]
                          },
                            // {
                            //     href: '/contracts',
                            //     title: this.$store.state.t('Contracts'),
                            //     accessLabelId: 'contracts',
                            //     attributes: {'hidden': true}
                            // },
                            // {
                            //     href: '/annexes',
                            //     title: this.$store.state.t('Annexes'),
                            //     accessLabelId: 'annexes',
                            //     attributes: {'hidden': true}
                            // },
                            // {
                            //     href: '/acts',
                            //     title: this.$store.state.t('Acts'),
                            //     accessLabelId: 'acts',
                            //     attributes: {'hidden': true}
                            // },
                            // {
                            //     href: '/accounts',
                            //     title: this.$store.state.t('Accounts'),
                            //     accessLabelId: 'accounts',
                            //     attributes: {'hidden': true}
                            // },
                            // {
                            //     href: '/financeCalendar',
                            //     title: this.$store.state.t('Finance Calendar'),
                            //     accessLabelId: 'financeCalendar',
                            //     attributes: {'hidden': true}
                            // },
                        ]
                    },
                    {
                        title: this.$store.state.t('User Management'),
                        icon: 'pe-7s-id',
                        attributes: {'hidden': true},
                        child: [
                            {
                                href: '/users',
                                title: this.$store.state.t('Users'),
                                accessLabelId: 'users',
                                attributes: {'hidden': true}
                            },
                            {
                                title: this.$store.state.t('Roles'),
                                href: '/roles',
                                accessLabelId: 'roles',
                                attributes: {'hidden': true}
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
            var self= this;

            if (typeof (this.$store.state.user) !== 'undefined') {
                this.setUserMenu();
            }
            document.addEventListener('rerenderSidebar', function (data) {
                self.setUserMenu();
            });
        },
        methods: {

            setUserMenu: function () {

                axios.get(window.apiDomainUrl+'/site/get-user-menu-config', qs.stringify({}))
                    .then((response) => {
                        this.startCheckingMenu(this.menu, response.data.items, []);
                    });
            },

            checkMenuElement(array, accessableItems, parentElements) {
                var self = this;
                var hasVisibleElement = false;
                var accessableItemsList = accessableItems || [];

                array.forEach(function (element){
                    if (element.hasOwnProperty('child')) {
                            parentElements.push(element);
                            self.startCheckingMenu(element.child, accessableItemsList, parentElements);
                    } else {
                        if (element.hasOwnProperty('accessLabelId') && accessableItemsList.indexOf(element.accessLabelId) !== -1) {
                            element.attributes.hidden = false;
                            hasVisibleElement = true;
                        }
                    }
                });

                if (hasVisibleElement) {
                    parentElements.forEach(function (parentElement) {
                        parentElement.attributes.hidden = false;
                    });
                } else {
                    parentElements.pop()
                }

                return hasVisibleElement;
            },

            startCheckingMenu(array, accessableItems, parentElements) {
                this.checkMenuElement(array, accessableItems, parentElements);
            },

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

        computed: {

        },

        beforeDestroy() {
            window.removeEventListener('resize', this.getWindowWidth);
        }
    }
</script>
