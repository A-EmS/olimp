<template>
    <div>
        <div class="mb-3 card">
            <div class="card-header-tab card-header">
                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                    <i class="header-icon lnr-charts icon-gradient bg-happy-green"> </i>
                    {{$store.state.t('Portfolio Performance')}}
                </div>
            </div>
            <div class="no-gutters row">
                <div class="col-sm-6 col-md-4 col-xl-4">
                    <div class="card no-shadow rm-border bg-transparent widget-chart text-left">
                        <div class="icon-wrapper rounded-circle">
                            <div class="icon-wrapper-bg opacity-10 bg-warning"></div>
                            <i class="lnr-laptop-phone text-dark opacity-8"></i></div>
                        <div class="widget-chart-content">
                            <div class="widget-subheading">{{$store.state.t('Cash Deposits')}}</div>
                            <div class="widget-numbers">1,7M</div>
                            <div class="widget-description opacity-8 text-focus">
                                <div class="d-inline text-danger pr-1">
                                    <font-awesome-icon icon="angle-down"/>
                                    <span class="pl-1">54.1%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="divider m-0 d-md-none d-sm-block"></div>
                </div>
            </div>
        </div>
        <interface-language-radio-selector></interface-language-radio-selector>
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