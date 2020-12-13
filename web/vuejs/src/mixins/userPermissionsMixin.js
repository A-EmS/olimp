
export default {
    data: () => ({

    }),
    created: function() {

    },
    methods: {
        getUserPermission(permissionName) {
            return parseInt(this.$store.state.user[permissionName]) === 1;
        }
    }
}
