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
      '$route' (to, from) {
          if(this.$root.user === false && this.$router.currentRoute.name !== 'login'){
              this.$router.push({ name: "login" });
          }
      }
    },
    created: function() {
        window.axios = axios;
        window.j = $;
        window.qs = qs;
        window.r = this.$router;
        this.setUserData();
    },

    methods: {
        getUser: function () {
            return this.$root.user;
        },

        setUser: function (user) {
            this.$root.user = user;
        },

        setUserData: function () {
            axios.get('/site/user-data')
                .then((response) => {
                    this.setUser(response.data);
                    if(response.data === false && this.$router.currentRoute.name !== 'login'){
                        this.$router.push({ name: "login" });
                    } else if (response.data !== false && this.$router.currentRoute.name === 'login'){
                        this.$router.push({ name: "main" });
                    }
            });
        },
    }
  }
</script>

<style lang="scss">
  @import "assets/base.scss";
</style>
