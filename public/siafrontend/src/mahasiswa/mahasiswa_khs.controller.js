(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaKhsController', MahasiswaKhsController);

  MahasiswaKhsController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams', '$compile'];
  /* @ngInject */
  function MahasiswaKhsController($q, dataservice, logger, scope, stateParams, compile) {
    var vm = this;
    vm.title = 'KHS Mahasiswa';
    vm.data = [];

    vm.cetakKhs = cetakKhs;
    vm.detailKhs = detailKhs;

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/khs').then(function(response) {
        vm.data = response;
      });
    }

    function cetakKhs(data) {
      let url = `khs/${data.id}`;
      dataservice.getReport(url).then(function(response) {
        let base64 = response.content;
        scope.fileName = 'KHS.pdf';
        scope.type = 'pdf';
        scope.filetype = 'application/pdf';
        scope.base64 = base64;

        let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
        let el = compile(preview_modal)(scope);
      });
    }

    function detailKhs(data) {
      vm.active_data = data;
      let el = "<modal-detail-khs data='vm.active_data'></modal-detail-khs>";
      el = compile(el)(scope);
    }

  }
})();
