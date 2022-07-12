(function() {
  'use strict';

  angular
    .module('app.pendaftaran')
    .controller('PendaftaranDetailController', PendaftaranDetailController);

  PendaftaranDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$compile'];
  /* @ngInject */
  function PendaftaranDetailController($q, dataservice, logger, stateParams, scope, compile) {
    var vm = this;
    vm.title = 'Detail Setup PMB';
    vm.table = 'pendaftaran';
    vm.data = {};

    vm.terbitkanNIM = terbitkanNIM;
    vm.reportMahasiswa = reportMahasiswa;

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

    function terbitkanNIM() {
      return dataservice.postDataUrl('/terbitkan/nim', {'pendaftaran_id': vm.data.id}).then(function(response) {
        logger.success("NIM berhasil diterbitkan");
      });
    }

    function reportMahasiswa() {
      let url = `mahasiswa/user_login/${vm.data.id}`;
      dataservice.getReport(url).then(function(response) {
        let base64 = response.content;
        scope.fileName = 'Report Mahasiswa.pdf';
        scope.type = 'pdf';
        scope.filetype = 'application/pdf';
        scope.base64 = base64;

        let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
        let el = compile(preview_modal)(scope);
      });
    }
  }
})();
