(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .controller('DosenPaDetailController', DosenPaDetailController);

  DosenPaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope', '$element'];
  /* @ngInject */
  function DosenPaDetailController($q, dataservice, logger, stateParams, compile, scope, element) {
    var vm = this;
    vm.title = 'Detail Dosen PA';
    vm.table = 'dosen_pa';
    vm.data = {};

    vm.tambahMahasiswa = tambahMahasiswa;
    vm.prosesPengajuan = prosesPengajuan;
    vm.lihatKRS = lihatKRS;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
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

    function tambahMahasiswa() {
      let el = "<modal-tambah-mahasiswa></modal-tambah-mahasiswa>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function prosesPengajuan(data) {
      vm.active_data = data;
      let el = "<modal-proses-pengajuan data='vm.active_data'></modal-proses-pengajuan>";
      el = compile(el)(scope);
    }

    function lihatKRS(data) {
      vm.active_data = data;
      let el = "<modal-lihat-krs data='vm.active_data'></modal-lihat-krs>";
      el = compile(el)(scope);
    }
  }
})();
