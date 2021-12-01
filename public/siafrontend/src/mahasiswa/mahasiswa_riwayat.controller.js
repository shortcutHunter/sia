(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaRiwayatController', MahasiswaRiwayatController);

  MahasiswaRiwayatController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaRiwayatController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'Riwayat Belajar Mahasiswa';
    vm.data = [];

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/riwayat').then(function(response) {
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
