Dropzone.autoDiscover = false;
// or disable for specific dropzone:
// Dropzone.options.myDropzone = false;

var files = [];
var myDropzone = null;
$(function () {
    // Now that the DOM is fully loaded, create the dropzone, and setup the
    // event listeners
    myDropzone = new Dropzone("#my-dropzone");
    myDropzone.on("addedfile", function (file) {
        let currentImages = $('form').find('input#images').val();

        if (currentImages !== '')
            $('form').find('input#images').val(currentImages + "," + file.name);
        else
            $('form').find('input#images').val(file.name);
    });
    myDropzone.on("complete", function (file) {
        files.push(file);
    });
});