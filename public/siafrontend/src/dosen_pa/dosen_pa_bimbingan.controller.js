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
    vm.lihatKRS = lihatKRS;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/dosen/mahasiswa/bimbingan').then(function(response) {
        vm.data = response;
      });
    }

    function prosesPengajuan(data) {
      vm.active_data = data;
      let el = "<modal-proses-pengajuan data='vm.active_data'></modal-proses-pengajuan>";
      el = compile(el)(scope);
    }

    function lihatKRS(data) {
      vm.active_data = data;
      vm.data.semester_id = data.mahasiswa.semester_id;
      let el = "<modal-lihat-krs data='vm.active_data'></modal-lihat-krs>";
      el = compile(el)(scope);
    }
  }
})();
