(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('MahasiswaTagihanDetailController', MahasiswaTagihanDetailController);

  MahasiswaTagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$state', '$compile'];
  /* @ngInject */
  function MahasiswaTagihanDetailController($q, dataservice, logger, stateParams, scope, state, compile) {
    var vm = this;
    vm.title = 'Detail Mahasiswa Tagihan';
    vm.table = 'tagihan';
    vm.data = {};
    vm.option = {};
    vm.verifiedTrans = [];
    vm.canCicil = true;

    vm.tambahPembiayaan = tambahPembiayaan;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;

        vm.verifiedTrans = [];

        $.each(vm.data.transaksi, (i, v) => {
          if (v.status == 'verified') {
            vm.verifiedTrans.push(v);
          } else {
            vm.canCicil = false;
          }
        });
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function tambahPembiayaan() {
      let el = "<modal-bayar-tagihan data='vm.data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
    }

  }
})();
