(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .controller('DosenPaDetailController', DosenPaDetailController);

  DosenPaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function DosenPaDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Dosen PA';
    vm.table = 'dosen_pa';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
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
