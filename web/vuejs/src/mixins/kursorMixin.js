export default {
    methods: {
        setCaretToEnd: function(ref, inputModel){
            ref.setCaretPosition(inputModel.length);
        },
    }
}

