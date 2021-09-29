(function() {
  'use strict';

  angular
    .module('app.karyawan')
    .controller('KaryawanDetailController', KaryawanDetailController);

  KaryawanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function KaryawanDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Karyawan';
    vm.table = 'karyawan';
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
