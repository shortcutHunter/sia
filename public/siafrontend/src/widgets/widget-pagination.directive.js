(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetPagination', widgetPagination);

  widgetPagination.$inject = ['dataservice'];

  /* @ngInject */
  function widgetPagination(dataservice) {
    var directive = {
      scope: {
        pageData: '='
      },
      templateUrl: '/template/widget/pagination',
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      let m = scope;
      m.dm = {};
      m.dm.pageData = scope.pageData;

      $(element).on('click', 'a', function() {
        let page = $(this).attr('page');
        scope.$parent.vm.page = page;
        scope.$apply();
      });


      scope.$watch(function() {
        return scope.pageData
      }, 
      function(newVal) {
        if (newVal) {
          m.dm.pageData = newVal;
        }
      });
    }
  }
})();
