/**
 __   _      ___       _____   __   _   
 | || | ||   / _ \\    / ___// | || | || 
 | '--' ||  / //\ \\   \___ \\ | '--' || 
 | .--. || |  ___  ||  /    // | .--. || 
 |_|| |_|| |_||  |_|| /____//  |_|| |_|| 
 `-`  `-`  `-`   `-` `-----`   `-`  `-`  
 _____     ____      _____      ___      ______    _____   
 / ____||  |  _ \\   |  ___||   / _ \\   /_   _//  |  ___|| 
 / //---`'  | |_| ||  | ||__    / //\ \\  `-| |,-   | ||__   
 \ \\___    | .  //   | ||__   |  ___  ||   | ||    | ||__   
 \_____||  |_|\_\\   |_____|| |_||  |_||   |_||    |_____|| 
 `----`   `-` --`   `-----`  `-`   `-`    `-`'    `-----`  
 
 Copyright 2012 Omar Massad, HashCreate.com
 
 Version: 1.0 
 
 This software is licensed under the Apache License, Version 2.0 (the "Apache License") or the GNU
 General Public License version 2 (the "GPL License"). You may choose either license to govern your
 use of this software only upon the condition that you accept all of the terms of either the Apache
 License or the GPL License.
 
 You may obtain a copy of the Apache License and the GPL License at:
 
 http://www.apache.org/licenses/LICENSE-2.0
 http://www.gnu.org/licenses/gpl-2.0.html
 
 Unless required by applicable law or agreed to in writing, software distributed under the
 Apache License or the GPL Licesnse is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 CONDITIONS OF ANY KIND, either express or implied. See the Apache License and the GPL License for
 the specific language governing permissions and limitations under the Apache License and the GPL License.
 */

(function($) {


 function displayMessage(name, action, message, messageTimer) {
  //$("" + name).html('<a class="close" data-dismiss="alert">Ã—</a>');

  if (action === 0) {
   $("" + name).hide();
  } else if (action === 1) {
   $("" + name).show().attr('class', 'alert alert-success');
  } else if (action === 2) {
   $("" + name).show().attr('class', 'alert alert-danger');
  } else if (action === 3) {
   if (messageTimer > 0) {
    setTimeout(function() {
     $("" + name).fadeOut('fast');
    }, messageTimer);
   }
   $("" + name).html("" + message);
  }
 }

 function loadingImg(btn, name, path, action) {
  if (action === 1) {
   btn.show();
   btn.prop('disabled', '');
   $("img#loading_" + name).remove();
  } else {
   btn.hide();
   btn.prop('disabled', 'disabled');
   $("" + path).insertAfter(btn);
  }
 }

 $.fn.hashForm = function(options) {

  options = $.extend({}, $.fn.hashForm.defaultOptions, options);

  this.each(function() {
   var element = $(this);
  });

  displayMessage(options.message, 0, 0, 0);
  loadingImg(options.btn, options.loadingName, "<img src='" + options.loadingImagePath + "' alt='Loading...' title='Loading...' id='loading_" + options.loadingName + "' class='loading_image'>", 0);

  return $.ajax({
   data: options.form,
   type: options.type,
   url: "" + options.url,
   dataType: "json",
   success: function(data) {

    loadingImg(options.btn, options.loadingName, "", 1);
    if (data.status === '1') {
     displayMessage(options.message, 1, 0, 0);
    } else {
     $(data.focus).focus();
     displayMessage(options.message, 2, 0, 0);
    }
    displayMessage(options.message, 3, data.message, options.messageTimer);
   }
  });

 };

 $.fn.hashForm.defaultOptions = {
  form: "",
  type: "post",
  url: "",
  btn: $(this),
  message: "",
  loadingName: "",
  loadingImagePath: "images/ajax-loader.gif",
  messageTimer: "0"
 };



 $.fn.hashAlert = function(options) {

  options = $.extend({}, $.fn.hashAlert.defaultOptions, options);

  this.each(function() {
   var element = $(this);
  });

  if (options.type == "fail") {
   options.cancelText = "Ok";
  }

  obj = {
   type: options.type,
   header: options.header,
   text: options.text,
   message_id: options.message_id,
   cancelText: options.cancelText,
   buttons: options.buttons
  };

  $("#alertModal").modal('hide').remove();
  var alertModalTemplate = doT.template($('#alertModalTemplate').html());
  $('body').append(alertModalTemplate(obj));
  $("#alertModal").modal('show');

 };

 $.fn.hashAlert.defaultOptions = {
  type: "fail",
  header: "Sorry but,",
  text: "",
  message_id: "",
  cancelText: "Close",
  buttons: ""
 };




 $.fn.hashFlip = function(options) {

  var options = $.extend({
   front: null,
   back: null,
   complete: null
  }, options);

  var element;
  return this.each(function() {
   element = $(this);
   element.attr("status", "0");
   options.back.hide();
   element.click(onClick);
  });


  function onClick() {
   if (element.attr("status") === "1") {
    options.front.slideDown();
    options.back.slideUp();
    element.attr("status", "0");
   } else {
    options.front.slideUp();
    options.back.slideDown();
    element.attr("status", "1");
   }

   if ($.isFunction(options.complete)) {
    options.complete.call(this);
   }
  }

 };


})(jQuery);