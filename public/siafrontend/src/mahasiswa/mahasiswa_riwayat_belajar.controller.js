(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaRiwayatBelajarController', MahasiswaRiwayatBelajarController);

  MahasiswaRiwayatBelajarController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaRiwayatBelajarController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'Riwayat Belajar Mahasiswa';
    vm.table = 'riwayat_belajar';
    vm.data = [];
    vm.pageData = {};

    activate();

    function activate() {
      getData();
    }

    function getData() {
      let filter = `mahasiswa_id=${stateParams.dataId}`;
      return dataservice.getDataFilter(vm.table, filter).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
