(function() {
  'use strict';

  angular
    .module('app.role')
    .controller('RoleDetailController', RoleDetailController);

  RoleDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function RoleDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Role';
    vm.table = 'role';
    vm.data = {};
    vm.getDataDetail = getDataDetail;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
