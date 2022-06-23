(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaTagihanController', MahasiswaTagihanController);

  MahasiswaTagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams', '$compile', '$state'];
  /* @ngInject */
  function MahasiswaTagihanController($q, dataservice, logger, scope, stateParams, compile, state) {
    var vm = this;
    vm.title = 'Tagihan Mahasiswa';
    vm.data = [];

    vm.bayarTagihan = bayarTagihan;
    vm.cetakTagihan = cetakTagihan;
    vm.detailTagihan = detailTagihan;

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/tagihan').then(function(response) {
        vm.data = response;
      });
    }

    function bayarTagihan(data) {
      vm.active_data = data;
      let el = "<modal-bayar-tagihan data='vm.active_data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
    }

    function cetakTagihan() {
      let url = `tagihan/mahasiswa`;
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

    function detailTagihan(data) {
      state.go('mahasiswa_tagihan_detail', {'dataId': data.id});
    }

  }
})();
