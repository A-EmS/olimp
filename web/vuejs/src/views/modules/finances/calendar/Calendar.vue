<template>
  <div>
    <page-title :buttonActionHide="true" :button-action-hide="getACL().create !== true" :heading="$store.state.t('Calendar')" :subheading="$store.state.t('Calendar actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card class="main-card mb-3">
      <b-button v-on:click="saveDataSet" v-if="showSaveButton && getACL().update" class="mr-2 mb-3 col-md-12" variant="success">
        {{$store.state.t('SAVE NEW DATASET')}}
      </b-button>

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
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import accessMixin from "../../../../mixins/accessMixin";
  import YearCalendar from '../../../components/year-calendar/YearCalendar'
  import {CalendarManager} from "../../../../managers/CalendarManager";
  import loadercustom from "../../../components/loadercustom";

  export default {
    components: {
      PageTitle,
      YearCalendar,
      loadercustom
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'calendar',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',

      showSaveButton: false,
      calendarDataIsNotEmpty: true,

      year: new Date().getFullYear(),
      activeDates: [],
      disabledDates: [],

      showYearSelector: true,
      activeClass: 'red',

      country_id: null,
      countryItems: [],
    }),

    created: function() {
      this.calendarManager = new CalendarManager();
      this.loadACL(this.accessLabelId);
      this.initCalendar();
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
        this.calendarManager.getByYearAndCountry({"year": this.year, "countryId": this.country_id})
            .then( (response) => {
              if(response.data !== false){
                this.activeDates = response.data.items;
                this.disabledDates = [];
                this.showSaveButton = false;
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
        if (dateInfo.selected === false) {
          if (this.disabledDates.indexOf(dateInfo.date) === -1) {
            this.disabledDates.push(dateInfo.date);
          }
        } else {
          var index = this.disabledDates.indexOf(dateInfo.date);
          if (index !== -1) {
            delete(this.disabledDates[index]);
          }
        }

        this.disabledDates = this.disabledDates.filter(function (el) {
          return el != '';
        });

        this.showSaveButton = true;
      },

      saveDataSet () {
        var activeDates = [];

        this.activeDates.forEach(function (item) {
          activeDates.push(item.date);
        });

        var updateData = {
          activeDates: activeDates,
          disabledDates: this.disabledDates,
          countryId: this.country_id,
        }

        this.showCustomLoaderDialog = true;
        this.calendarManager.update(updateData)
            .then( (response) => {
              if(response.data !== false){
                this.showCustomLoaderDialog = false;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      }
    },

    beforeDestroy () {

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