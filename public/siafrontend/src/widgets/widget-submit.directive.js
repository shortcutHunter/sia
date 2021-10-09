(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetSubmit', widgetSubmit);

  widgetSubmit.$inject = ['dataservice', '$location', '$state'];

  /* @ngInject */
  function widgetSubmit(dataservice, location, state) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        var vm = scope.vm;
        let current_state = state.router.globals.current;
        let state_name = current_state.name;
        state_name = state_name.split("_form").shift();
        state_name = state_name.split("_detail").shift();
        dataservice.postData(vm.table, vm.data, vm.data.id).then(function(data){
          location.path(`/${state_name}/${data.id}`);
        });
      });
    }
  }
})();
