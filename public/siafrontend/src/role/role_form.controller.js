(function() {
  'use strict';

  angular
    .module('app.role')
    .controller('RoleFormController', RoleFormController);

  RoleFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function RoleFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Role';
    vm.table = 'role';

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
