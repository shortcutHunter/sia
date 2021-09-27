(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetTr', widgetTr);

  widgetTr.$inject = ['$state'];

  /* @ngInject */
  function widgetTr(state) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let id = attr.trid;
        let stateName = attr.stateName;
        state.go(stateName, {'dataId': id});
      });
    }
  }
})();
