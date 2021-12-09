(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .controller('DosenPaBimbinganController', DosenPaBimbinganController);

  DosenPaBimbinganController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope', '$element'];
  /* @ngInject */
  function DosenPaBimbinganController($q, dataservice, logger, stateParams, compile, scope, element) {
    var vm = this;
    vm.title = 'Mahasiswa Bimbingan';
    vm.data = {};

    vm.prosesPengajuan = prosesPengajuan;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/dosen/mahasiswa/bimbingan').then(function(response) {
        vm.data = response;
        logger.info('Data loaded');
      });
    }

    function prosesPengajuan(data) {
      vm.active_data = data;
      let el = "<modal-proses-pengajuan data='vm.active_data'></modal-proses-pengajuan>";
      el = compile(el)(scope);
    }
  }
})();
