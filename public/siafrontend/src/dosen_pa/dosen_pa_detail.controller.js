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
    vm.migrasiMahasiswa = migrasiMahasiswa;
    vm.prosesPengajuan = prosesPengajuan;
    vm.lihatKRS = lihatKRS;
    vm.cetakMahasiswa = cetakMahasiswa;
    vm.liatTagihan = liatTagihan;

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

    function migrasiMahasiswa() {
      let el = "<modal-migrasi-mahasiswa data='vm.data'></modal-migrasi-mahasiswa>";
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

    function cetakMahasiswa() {
      let url = `dosen/pa/${vm.data.id}/mahasiswa/bimbingan`;
      dataservice.getReport(url).then(function(response) {
        let base64 = response.content;
        scope.fileName = 'List Mahasiswa Bimbingan.pdf';
        scope.type = 'pdf';
        scope.filetype = 'application/pdf';
        scope.base64 = base64;

        let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
        let el = compile(preview_modal)(scope);
      });
    }

    function liatTagihan(data) {
      let url = `tagihan/mahasiswa/${data.mahasiswa_id}`;
      dataservice.getReport(url).then(function(response) {
        let base64 = response.content;
        scope.fileName = 'Tagihan Mahasiswa.pdf';
        scope.type = 'pdf';
        scope.filetype = 'application/pdf';
        scope.base64 = base64;

        let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
        let el = compile(preview_modal)(scope);
      });
    }
  }
})();
