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
    vm.total_row = 0;
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
        $.each(vm.data, (i, v) => {
          vm.total_row += v.riwayat_belajar_detail.length || 0;
        });
      });
    }

  }
})();
