<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Calendar')" :subheading="$store.state.t('Calendar actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card class="main-card mb-3">
      <full-calendar :events="clendar.events" :config="clendar.config" :header="clendar.header"></full-calendar>
    </b-card>

  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import accessMixin from "../../../../mixins/accessMixin";
  import { FullCalendar } from 'vue-full-calendar'

  export default {
    components: {
      PageTitle,
      FullCalendar
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'calendar',

      updateItemListEventName: 'updateList:product',
      createProcessName: 'create:product',

      clendar: {
        header: {
          left:   'prev,next today',
          center: 'title',
        },

// $(".date").datepicker({
// onSelect: function(dateText) {
// var selectedDate = this.value;
// $('#calendar').fullCalendar( 'gotoDate', selectedDate );
// }
// });

        config: {
          // weekends: false,
          monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
          monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
          locale: 'RU',
          defaultView: 'month',

          eventBackgroundColor: 'deeppink',
          eventBorderColor: 'deeppink',
          eventTextColor: 'white',

          buttonText: {
            today: 'Сегодня',
          },

          eventClick: function(event) {
            console.log(event);
            alert(1);
          },

          select: function (start, end) {
            console.log(start);
            console.log(end);
            alert(3);
          },
          viewRender: function(view, element) {
            // alert(111);
            console.log("The view's title is " + view.intervalStart.format());
            console.log("The view's title is " + view.name);
          },
        },

        events: [
          {
            id: 'id1',
            title  : 'Выходной',
            start  : '2020-07-07',
            rendering: 'background',
            backgroundColor: '#F00',
            textColor: 'white'
          },
          {
            id: 'id2',
            title  : 'Выходной',
            start  : '2020-07-09',
            end    : '2020-07-12',
          },
          {
            id: 'id3',
            title  : 'Выходной',
            start  : '2020-07-15',
          },
        ],
      },

    }),

    created: function() {

      this.loadACL(this.accessLabelId);

    },

    methods: {

    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
    },

    filters: {

    },
    computed: {

    }
  }
</script>

<style>
  .fc-sat, .fc-sun {
    background-color: lightpink !important;
  }
</style>