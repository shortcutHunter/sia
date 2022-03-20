(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetDatePicker', widgetDatePicker);

  widgetDatePicker.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function widgetDatePicker(state, dataservice, compile) {
    var directive = {
      restrict: 'C',
      link: link,
      require : 'ngModel'
    };
    return directive;

    function link(scope, element, attrs, ctrl) {

      activate();

      function activate () {
        $(element).inputmask();
      }

    };
  }
})();
