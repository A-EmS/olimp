<template>
    <div>
        <page-title :heading=heading :subheading=subheading :icon=icon></page-title>
        <div class="mb-3 card">
            <div class="card-header-tab card-header">
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                    <i class="header-icon lnr-charts icon-gradient bg-happy-green"> </i>
                    Portfolio Performance
                </div>
                <div class="btn-actions-pane-right text-capitalize">
                    <button class="btn-wide btn-outline-2x mr-md-2 btn btn-outline-focus btn-sm">View All</button>
                </div>
            </div>
            <div class="no-gutters row">
                <div class="col-sm-6 col-md-4 col-xl-4">
                    <div class="card no-shadow rm-border bg-transparent widget-chart text-left">
                        <div class="icon-wrapper rounded-circle">
                            <div class="icon-wrapper-bg opacity-10 bg-warning"></div>
                            <i class="lnr-laptop-phone text-dark opacity-8"></i></div>
                        <div class="widget-chart-content">
                            <div class="widget-subheading">Cash Deposits</div>
                            <div class="widget-numbers">1,7M</div>
                            <div class="widget-description opacity-8 text-focus">
                                <div class="d-inline text-danger pr-1">
                                    <font-awesome-icon icon="angle-down"/>
                                    <span class="pl-1">54.1%</span>
                                </div>
                                less earnings
                            </div>
                        </div>
                    </div>
                    <div class="divider m-0 d-md-none d-sm-block"></div>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-4">
                    <div class="card no-shadow rm-border bg-transparent widget-chart text-left">
                        <div class="icon-wrapper rounded-circle">
                            <div class="icon-wrapper-bg opacity-9 bg-danger"></div>
                            <i class="lnr-graduation-hat text-white"></i></div>
                        <div class="widget-chart-content">
                            <div class="widget-subheading">Invested Dividents</div>
                            <div class="widget-numbers"><span>9M</span></div>
                            <div class="widget-description opacity-8 text-focus">
                                Grow Rate:
                                <span class="text-info pl-1">
                                    <font-awesome-icon icon="angle-down"/>
                                    <span class="pl-1">14.1%</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="divider m-0 d-md-none d-sm-block"></div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-4">
                    <div class="card no-shadow rm-border bg-transparent widget-chart text-left">
                        <div class="icon-wrapper rounded-circle">
                            <div class="icon-wrapper-bg opacity-9 bg-success"></div>
                            <i class="lnr-apartment text-white"></i></div>
                        <div class="widget-chart-content">
                            <div class="widget-subheading">Capital Gains</div>
                            <div class="widget-numbers text-success"><span>$563</span></div>
                            <div class="widget-description text-focus">
                                Increased by
                                <span class="text-warning pl-1">
                                    <font-awesome-icon icon="angle-up"/>
                                    <span class="pl-1">7.35%</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center d-block p-3 card-footer">
                <button class="btn-pill btn-shadow btn-wide fsize-1 btn btn-primary btn-lg">
                  <span class="mr-2 opacity-7">
                      <i class="icon icon-anim-pulse ion-ios-analytics-outline"></i>
                  </span>
                    <span class="mr-1">View Complete Report</span>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <div class="mb-3 card">
                    <div class="card-header-tab card-header">
                        <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                            <i class="header-icon lnr-cloud-download icon-gradient bg-happy-itmeo"> </i>
                            Technical Support
                        </div>
                        <div class="btn-actions-pane-right text-capitalize actions-icon-btn">
                            <b-dropdown toggle-class="btn-icon btn-icon-only" variant="link" no-caret>
                                <span slot="button-content"><i class="pe-7s-menu btn-icon-wrapper"></i></span>
                                <ul class="nav flex-column">
                                    <li class="nav-item-header nav-item">Activity</li>
                                    <li class="nav-item"><a href="javascript:void(0);" class="nav-link">Chat
                                        <div class="ml-auto badge badge-pill badge-info">8</div>
                                    </a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" class="nav-link">Recover Password</a></li>
                                    <li class="nav-item-header nav-item">My Account</li>
                                    <li class="nav-item"><a href="javascript:void(0);" class="nav-link">Settings
                                        <div class="ml-auto badge badge-success">New</div>
                                    </a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" class="nav-link">Messages
                                        <div class="ml-auto badge badge-warning">512</div>
                                    </a></li>
                                    <li class="nav-item"><a href="javascript:void(0);" class="nav-link">Logs</a></li>
                                    <li class="nav-item-divider nav-item"></li>
                                    <li class="nav-item-btn nav-item">
                                        <button class="btn-wide btn-shadow btn btn-danger btn-sm">Cancel</button>
                                    </li>
                                </ul>
                            </b-dropdown>
                        </div>
                    </div>
                    <div class="p-0 card-body">
                        <div class="p-1 slick-slider-sm mx-auto">
                            <slick ref="slick" :options="slickOptions2">
                                <div class="widget-chart widget-chart2 text-left p-0">
                                    <div class="widget-chat-wrapper-outer">
                                        <div class="widget-chart-content widget-chart-content-lg pb-0">
                                            <div class="widget-chart-flex">
                                                <div class="widget-title opacity-5 text-muted text-uppercase">Helpdesk Tickets</div>
                                            </div>
                                            <div class="widget-numbers">
                                                <div class="widget-chart-flex">
                                                    <div>
                                                        <span class="text-warning">34</span>
                                                    </div>
                                                    <div class="widget-title ml-2 font-size-lg font-weight-normal text-dark">
                                                        <span class="opacity-5 text-muted pl-2 pr-1">5%</span>
                                                        increase
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-chart-wrapper he-auto opacity-10 m-0">
                                            <chart1 :height="145"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-chart widget-chart2 text-left p-0">
                                    <div class="widget-chat-wrapper-outer">
                                        <div class="widget-chart-content widget-chart-content-lg pb-0">
                                            <div class="widget-chart-flex">
                                                <div class="widget-title opacity-5 text-muted text-uppercase">New Accounts since 2018</div>
                                            </div>
                                            <div class="widget-numbers">
                                                <div class="widget-chart-flex">
                                                    <div>
                                                        <span class="opacity-10 text-success pr-2">
                                                            <font-awesome-icon icon="angle-up"/>
                                                        </span>
                                                        <span>78</span>
                                                        <small class="opacity-5 pl-1">%</small>
                                                    </div>
                                                    <div class="widget-title ml-2 font-size-lg font-weight-normal text-muted">
                                                        <span class="text-success pl-2">+14</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-chart-wrapper he-auto opacity-10 m-0">
                                            <chart2 :height="145"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="widget-chart widget-chart2 text-left p-0">
                                    <div class="widget-chat-wrapper-outer">
                                        <div class="widget-chart-content widget-chart-content-lg pb-0">
                                            <div class="widget-chart-flex">
                                                <div class="widget-title opacity-5 text-muted text-uppercase">Last Year Total Sales</div>
                                            </div>
                                            <div class="widget-numbers">
                                                <div class="widget-chart-flex">
                                                    <div>
                                                        <small class="opacity-3 pr-1">$</small>
                                                        <span>629</span>
                                                        <span class="text-primary pl-3">
                                                            <font-awesome-icon icon="angle-down"/>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-chart-wrapper he-auto opacity-10 m-0">
                                            <chart3 :height="145"/>
                                        </div>
                                    </div>
                                </div>

                            </slick>
                        </div>

                        <h6 class="text-muted text-uppercase font-size-md opacity-5 pl-3 pr-3 pb-1 font-weight-normal">Sales Progress</h6>
                        <ul class="list-group list-group-flush">
                            <li class="p-3 bg-transparent list-group-item">
                                <div class="widget-content p-0">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Total Orders</div>
                                                <div class="widget-subheading">Last year expenses</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="widget-numbers text-success">
                                                    <small>$</small>
                                                    1896
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-progress-wrapper">
                                            <div class="progress-bar-sm progress-bar-animated-alt progress">
                                                <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100" style="width: 43%;"></div>
                                            </div>
                                            <div class="progress-sub-label">
                                                <div class="sub-label-left">YoY Growth</div>
                                                <div class="sub-label-right">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <layout-wrapper>
            <demo-card heading="Scrollable" subheading="Example of a dialog with scrollable content.">
                <interface-language-radio-selector></interface-language-radio-selector>
            </demo-card>
        </layout-wrapper>
    </div>
</template>

<script>

    import PageTitle from "../../Layout/Components/PageTitle";
    import VuePerfectScrollbar from 'vue-perfect-scrollbar'
    import Slick from 'vue-slick';

    import Trend from 'vuetrend';

    import chart1 from "../../DemoPages/Dashboards/Analytics/chart1";
    import chart2 from "../../DemoPages/Dashboards/Analytics/chart2";
    import chart3 from "../../DemoPages/Dashboards/Analytics/chart3";

    import LayoutWrapper from '../../Layout/Components/LayoutWrapper';
    import DemoCard from "../../Layout/Components/DemoCard";
    import interfaceLanguageRadioSelector from "../components/interfaceLanguageRadioSelector";

    import {library} from '@fortawesome/fontawesome-svg-core'
    import {
        faTrashAlt,
        faCheck,
        faCalendarAlt,
        faAngleDown,
        faAngleUp,
        faTh,
    } from '@fortawesome/free-solid-svg-icons'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

    library.add(
        faTrashAlt,
        faCheck,
        faAngleDown,
        faAngleUp,
        faTh,
        faCalendarAlt,
    );

    export default {
        components: {
            PageTitle,
            VuePerfectScrollbar,
            Slick,
            'font-awesome-icon': FontAwesomeIcon,
            'trend': Trend,
            chart1,
            chart2,
            chart3,
            DemoCard,
            interfaceLanguageRadioSelector,
            LayoutWrapper

        },
        data: () => ({
            heading: 'Analytics Dashboard',
            subheading: 'This is an example dashboard created using build-in elements and components.',
            icon: 'pe-7s-plane icon-gradient bg-tempting-azure',

            slickOptions2: {
                slidesToShow: 1,
                dots: true,
            },

            fields: [
                {
                    key: 'last_name',
                    sortable: true
                },
                {
                    key: 'first_name',
                    sortable: true
                },
                {
                    key: 'age',
                    label: 'Person age',
                    sortable: true,
                    // Variant applies to the whole column, including the header and footer
                }
            ],
            items: [
                { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
                { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
                { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
                { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
            ]

        }),

        methods: {
        },

    }
</script>