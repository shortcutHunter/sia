(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .controller('DosenPaFormController', DosenPaFormController);

  DosenPaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function DosenPaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Dosen PA';
    vm.table = 'dosen_pa';

    activate();

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }
  }
})();
