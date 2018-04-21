var utilities = {
    notify: function (title, message, className, sticky) {
        $.gritter.add({
            title: title,
            text: message,
            class_name: className,
            sticky: sticky
        });
    },
    formatThousand: function (number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}