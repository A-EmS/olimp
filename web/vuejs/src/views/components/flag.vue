<template>
    <div>
        <div v-if="!span" :style="styleString">
            <img :src="imgUrl" />
        </div>
        <span v-if="span" :style="styleString">
            <img :src="imgUrl" />
        </span>
    </div>
</template>

<script>
    export default {
        components: {

        },
        props: {
            countryAcronym: String,
            styleString: {
                type: String,
                required: false,
                default: ''
            },
            span: {
                type: Boolean,
                required: false,
                default: false
            },
        },
        methods: {

        },
        computed: {
            imgUrl() {
                return window.apiDomainUrl+'/img/flags/'+this.countryAcronym+'.png';
            }
        }
    }
</script>
