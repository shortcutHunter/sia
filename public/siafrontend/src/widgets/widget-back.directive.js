(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetBack', widgetBack);

  /* @ngInject */
  function widgetBack() {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        history.back();
      });
    }
  }
})();
