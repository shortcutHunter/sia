import swal from 'sweetalert';

(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaDetailController', MahasiswaDetailController);

  MahasiswaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope', '$state'];
  /* @ngInject */
  function MahasiswaDetailController($q, dataservice, logger, stateParams, compile, element, scope, state) {
    var vm = this;
    vm.title = 'Detail Mahasiswa';
    vm.table = 'mahasiswa';
    vm.data = {};

    vm.regUlang     = regUlang;
    vm.pengajuanKrs = pengajuanKrs;
    vm.bayarTagihan = bayarTagihan;
    vm.cetakKhs     = cetakKhs;
    vm.detailKhs    = detailKhs;
    vm.lulus        = lulus;
    vm.doMahasiswa  = doMahasiswa;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
        let promises = [getTagihan()];

        return $q.all(promises);

        function getTagihan() {
          let filter = `orang_id=${vm.data.orang_id}`;
          return dataservice.getDataFilter('tagihan', filter).then(function(response){
            vm.data.tagihan = response.data;
          });
        }
          
      });
    }

    function regUlang() {
      let el = "<modal-register-ulang></modal-register-ulang>";
      el = compile(el)(scope);
      // $(element).append(el);
    }

    function pengajuanKrs() {
      let el = "<modal-pengajuan-krs></modal-pengajuan-krs>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function bayarTagihan(data) {
      vm.active_data = data;
      let el = "<modal-bayar-tagihan data='vm.active_data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
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

        dataservice.getPdf(base64).then(function(file){
          el.find('#pdf-container').append(file);
        });
      });
    }

    function detailKhs(data) {
      vm.active_data = data;
      let el = "<modal-detail-khs data='vm.active_data'></modal-detail-khs>";
      el = compile(el)(scope);
    }

    function lulus() {
      swal({
        title: "Apakah anda yakin ingin meluluskan mahasiswa ini ?",
        icon: "warning",
        buttons: {
          cancel: true,
          text: "Iya"
        }
      })
      .then(yes => {
        if (yes) {
          let url = '/luluskan/mahasiswa';
          let data = {
            'mahasiswa_id': vm.data.id
          };
          dataservice.postDataUrl(url, data).then(function(data){
            state.reload();
          });
        }
      });
    }

    function doMahasiswa() {
      swal({
        title: "Apakah anda yakin ingin DO mahasiswa ini ?",
        icon: "warning",
        buttons: {
          cancel: true,
          text: "Iya"
        }
      })
      .then(yes => {
        if (yes) {
          let url = '/do/mahasiswa';
          let data = {
            'mahasiswa_id': vm.data.id
          };
          dataservice.postDataUrl(url, data).then(function(data){
            state.reload();
          });
        }
      });
    }

  }
})();
