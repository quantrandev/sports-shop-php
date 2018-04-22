Dropzone.autoDiscover = false;
// or disable for specific dropzone:
// Dropzone.options.myDropzone = false;

$(function () {
    // Now that the DOM is fully loaded, create the dropzone, and setup the
    // event listeners
    var myDropzone = new Dropzone("#my-dropzone");
    myDropzone.on("addedfile", function (file) {
        let currentImages = $('form').find('input#images').val();

        if (currentImages !== '')
            $('form').find('input#images').val(currentImages + "," + file.name);
        else
            $('form').find('input#images').val(file.name);
        console.log($('form').find('input#images').val());
    });
});