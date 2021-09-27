(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetSubmit', widgetSubmit);

  widgetSubmit.$inject = ['dataservice', '$location'];

  /* @ngInject */
  function widgetSubmit(dataservice, location) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        var vm = scope.vm;
        dataservice.postData(vm.table, vm.data, vm.data.id).then(function(data){
          location.path(`/${vm.table}/${data.id}`);
        });
      });
    }
  }
})();
