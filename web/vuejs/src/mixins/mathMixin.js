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
            number = number.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, " ");

            if (!number.includes('.')){
                number = number + '.00'
            }

            return number;
        }
    }
}

