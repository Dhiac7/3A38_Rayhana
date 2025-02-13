(function($) {
  'use strict';
  if ($("#fileuploader").length) {
    $("#fileuploader").uploadFile({
      url: "./images/",
      fileName: "myfile"
    });
  }
})(jQuery);