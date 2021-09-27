var moment = require('moment');
var toastr = require('toastr');


(function() {
  'use strict';

  angular
    .module('app.core')
    .constant('toastr', toastr)
    .constant('moment', moment);
})();
