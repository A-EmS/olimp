<template>
  <div id="app">
    <component :is="layout">
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </component>
  </div>
</template>

<script>
  const default_layout = "default";
  import axios from 'axios';
  import $ from 'jquery';
  import qs from 'qs';

  export default {
    computed: {
      layout() {
        return (this.$route.meta.layout || default_layout) + '-layout';
      }
    },
    data () {
        return {
            currentRoute: this.$router.currentRoute
        }
    },
    watch: {
      '$route' () {
          if(this.$store.state.user === false && (this.$router.currentRoute.name !== 'login' || window.location.href.substr(-3) === '/#/')){
              this.$router.push({ name: "login" });
          }
      }
    },
    beforeCreate() {
      window.config = {};
      window.config.time_popup = 150;
    },
    created: function() {
        window.axios = axios;
        window.j = $;
        window.qs = qs;
        window.r = this.$router;
        window.scrollToTop = function(time){
            var time = time || 400;
            window.j('html, body').animate({scrollTop: 0}, time);
        };

        // window.apiDomainUrl = 'http://olimp.loc';
        window.apiDomainUrl = '';
        this.setUserData();
    },

    methods: {
        getUser: function () {
            return this.$store.state.user;
        },

        setUser: function (user) {
            this.$store.state.user = user;
        },

        setUserData: function () {
            axios.get(window.apiDomainUrl+'/site/user-data', qs.stringify({}))
                .then((response) => {
                    // response.data = {"id":"2","username":"kaa","level":"80","authKey":"","accessToken":"","role":"0","roles":["0"],"settings":{"interface_language":"ru"},"isAdmin":true}
                    this.setUser(response.data);
                    if(response.data === false && this.$router.currentRoute.name !== 'login'){
                        this.$router.push({ name: "login" });
                    } else if (response.data !== false && (window.location.href.includes('/login') || window.location.href.substr(-3) === '/#/')){
                        this.$router.push({ name: "main" });
                    }
                    document.dispatchEvent(new CustomEvent('rerenderSidebar', {detail: {}}))
            });
        },
    }
  }
</script>

<style lang="scss">
  @import "assets/base.scss";
</style>
