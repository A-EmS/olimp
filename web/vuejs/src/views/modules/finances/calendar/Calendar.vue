<template>
  <div>
    <page-title :buttonActionHide="true" :button-action-hide="getACL().create !== true" :heading="$store.state.t('Calendar')" :subheading="$store.state.t('Calendar actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card class="main-card mb-3">
      <b-button v-on:click="generateDataForCalendar" v-if="!calendarDataIsNotEmpty && getACL().create" class="mr-2 mb-3 col-md-12" variant="success">
        {{$store.state.t('GENERATE DATA FOR CALENDAR')}}
      </b-button>

      <v-alert
              v-if="listOnlyAccess()"
              :value="true"
              color="info"
              icon="info"
              outline
      >
        {{$store.state.t('You have permissions only for view')}}
      </v-alert>

      <v-autocomplete
              v-model="country_id"
              :items="countryItems"
              item-value="id"
              item-text="name"
              :label="$store.state.t('Country')"
              @change="setCalendarActiveDates"
      ></v-autocomplete>

      <YearCalendar
              v-model="year"
              v-if="calendarDataIsNotEmpty"
              :activeDates.sync ="activeDates"
              :editable ="updateAccess()"
              :minYear ="2015"
              :maxYear ="2030"
              @toggleDate="toggleDate"
              @changeYear="setCalendarActiveDates"
              lang="ru"
              :showYearSelector="showYearSelector"
              :activeClass="activeClass"
              prefixClass=""
      ></YearCalendar>
    </b-card>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
    <calendar-form :formDataItem.sync="calendarData"></calendar-form>
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import accessMixin from "../../../../mixins/accessMixin";
  import YearCalendar from '../../../components/year-calendar/YearCalendar'
  import {CalendarManager} from "../../../../managers/CalendarManager";
  import loadercustom from "../../../components/loadercustom";
  import calendarForm from "./calendarForm";

  export default {
    components: {
      PageTitle,
      YearCalendar,
      loadercustom,
      calendarForm
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'calendar',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',

      calendarData: {},

      calendarDataIsNotEmpty: true,

      year: new Date().getFullYear(),
      activeDates: [],

      showYearSelector: true,
      activeClass: 'red',

      country_id: null,
      countryItems: [],
    }),

    created: function() {
      var self = this;
      this.calendarManager = new CalendarManager();
      this.loadACL(this.accessLabelId);
      this.initCalendar();

      this.$eventHub.$on('resultCalendarFormEvent', function () {
        self.saveDataSet();
      });
    },

    methods: {
      initCalendar () {
        this.calendarManager.getCountryList()
          .then( (response) => {
            if(response.data !== false){
              this.countryItems = response.data.items;
              var self = this;
              response.data.items.forEach(function (item) {
                if (typeof item.selected !== 'undefined' && item.selected === true) {
                  self.country_id = item.id;
                  self.showCustomLoaderDialog = true;
                  self.customDialogfrontString = self.$store.state.t('Loading Calendar Data');
                  self.setCalendarActiveDates();
                }
              })
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      },

      setCalendarActiveDates () {
        this.showCustomLoaderDialog = true;
        this.calendarManager.getByYearAndCountry({"year": this.year, "countryId": this.country_id})
            .then( (response) => {
              if(response.data !== false){
                this.activeDates = response.data.items;
                this.showCustomLoaderDialog = false;
                this.calendarDataIsNotEmpty = response.data.items.length > 0;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },

      generateDataForCalendar () {
        this.customDialogfrontString = this.$store.state.t('Generating Calendar Data');
        this.showCustomLoaderDialog = true;
        this.calendarManager.generateForCountry({"countryId": this.country_id})
                .then( (response) => {
                  if(response.data !== false){
                    this.customDialogfrontString = 'Please stand by'
                    this.showCustomLoaderDialog = false;
                    this.setCalendarActiveDates();
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      toggleDate (dateInfo) {
        this.calendarManager.getItemByDateAndCountry({'date': dateInfo.date, 'countryId': this.country_id})
                .then( (response) => {
                  if(response.data !== false){
                    response.data['selected'] = dateInfo.selected;
                    this.calendarData = response.data;
                    this.$eventHub.$emit('updateCalendarItemEvent', {});
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      saveDataSet () {
        if (this.calendarData.action === 'cancel') {

          if (this.calendarData.selected === true){
            var currentItemIndex = this.activeDates.indexOf(this.activeDates.find(obj => obj.date === this.calendarData.date));
            delete(this.activeDates[currentItemIndex]);
            this.activeDates = this.activeDates.filter(function (el) {
              return el != '';
            });
          } else {
            this.activeDates.push({'date': this.calendarData.date, 'className': 'red'})
          }

        } else if (this.calendarData.action === 'save') {
          if (this.calendarData.selected === true) {
            this.calendarData.day_off = 1;
          } else {
            this.calendarData.day_off = 0;
          }
          this.calendarManager.updateItemById(this.calendarData);
        }
      }
    },

    beforeDestroy () {
      this.$eventHub.$off('resultCalendarFormEvent');
    },
  }
</script>

<style>
  .red {
    background-color: red;
    color: white
  }
  .red:after {
    background-size: 100% 100%
  }
</style>