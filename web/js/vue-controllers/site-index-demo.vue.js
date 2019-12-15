var siteIndexDemo = new Vue({
    el: "#demoVueElement",

    props: {

    },
    data: function () {
        return {
            testModel: 'Вот так работает Vue.js'
        }
    },

    created: function () {

    },

    beforeMount: function() {

    },

    mounted: function(){

    },

    computed: {

    },

    methods: {

    },
    filters: {

    },
    template: `
        <div>
        <br />
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1" style="background: black; color: yellow;">Печатай здесь</span>
              <input v-model="testModel" type="text" class="form-control" placeholder="Печатай здесь">
            </div>
              {{ testModel }}
        </div>
    `
});
