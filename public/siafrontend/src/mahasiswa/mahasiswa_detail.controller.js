(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaDetailController', MahasiswaDetailController);

  MahasiswaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope'];
  /* @ngInject */
  function MahasiswaDetailController($q, dataservice, logger, stateParams, compile, element, scope) {
    var vm = this;
    vm.title = 'Detail Mahasiswa';
    vm.table = 'mahasiswa';
    vm.data = {};

    vm.regUlang = regUlang;
    vm.pengajuanKrs = pengajuanKrs;
    vm.bayarTagihan = bayarTagihan;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
        let promises = [getRiwayat(), getTagihan()];

        return $q.all(promises);

        function getRiwayat() {
          let filter = `mahasiswa_id=${vm.data.id}&status=nonaktif`;
          return dataservice.getDataFilter('riwayat_belajar', filter).then(function(response){
            vm.data.riwayat_belajar = response.data;
          });
        }

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
  }
})();
