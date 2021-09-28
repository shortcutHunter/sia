(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetUpdateStatus', widgetUpdateStatus);

  widgetUpdateStatus.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function widgetUpdateStatus(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.$parent.vm;
        let data = {status: attr.widgetUpdateStatus};
        let id = attr.targetId ? attr.targetId : vm.data.id;
        dataservice.postData(vm.table, data, id).then(function(data){
          state.reload();
        });
      });
    }
  }
})();
