(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetTr', widgetTr);

  widgetTr.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function widgetTr(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let id = attr.trid;
        let stateName = attr.stateName;

        dataservice.searchValue = scope.$parent.vm.searchValue;
        dataservice.filterValue = scope.$parent.vm.filterValue;

        state.go(stateName, {'dataId': id});
      });
    }
  }
})();
