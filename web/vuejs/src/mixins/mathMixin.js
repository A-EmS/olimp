export default {
    data: () => ({

    }),
    created: function() {

    },
    methods: {
        round: function (value, decimals) {
            return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
        },

        numberFormatThousandsSpace: function(number) {
            return number.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, " ")
        }
    }
}

